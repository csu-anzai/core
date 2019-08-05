<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\System\Messagequeue;

use Kajona\System\System\CoreEventdispatcher;
use Kajona\System\System\Database;
use Kajona\System\System\Messagequeue\Command\CallEventCommand;
use Psr\Log\LoggerInterface;

/**
 * Consumer
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.2
 */
class Consumer
{
    /**
     * Breaks in case the workflow takes more then this seconds
     */
    private const MAX_EXECUTION = 600;

    /**
     * Count of events which are max processed
     */
    private const MAX_PREFETCH = 50;

    /**
     * @var Database
     */
    private $connection;

    /**
     * @var CoreEventdispatcher
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Database $connection
     * @param CoreEventdispatcher $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(Database $connection, CoreEventdispatcher $eventDispatcher, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function consume(): void
    {
        $startTime = time();
        $result = $this->connection->getPArray('SELECT command_id, command_class, command_payload FROM agp_system_commands', [], 0, self::MAX_PREFETCH);

        foreach ($result as $row) {
            $class = $row['command_class'];
            $payload = \json_decode($row['command_payload'], true);

            // directly delete the event since otherwise this would block the queue in case the event throws an
            // unrecoverable error
            $this->connection->delete('agp_system_commands', ['command_id' => $row['command_id']]);

            // check whether the event class exists
            if (!class_exists($class)) {
                continue;
            }

            try {
                if (!is_callable($class . '::fromArray', false, $callable)) {
                    throw new \RuntimeException('Command has no fromArray method');
                }

                $command = call_user_func_array($callable, [$payload]);

                if ($command instanceof Command) {
                    $this->handleCommand($command);
                } else {
                    throw new \RuntimeException('Could not create command');
                }
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }

            // in case this workflow runs too long break and wait for the next execution
            if (time() - $startTime > self::MAX_EXECUTION) {
                break;
            }
        }
    }

    /**
     * Handles the event
     *
     * @TODO in the future if we have more events we want to create a factory service which creates the fitting executor
     * for the provided event
     *
     * @param Command $command
     */
    private function handleCommand(Command $command)
    {
        if ($command instanceof CallEventCommand) {
            $this->eventDispatcher->notifyGenericListeners($command->getName(), $command->getArguments());
        } else {
            throw new \RuntimeException('Could not handle command class');
        }
    }
}
