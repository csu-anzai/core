<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\System\Messagequeue;

use Kajona\System\System\CoreEventdispatcher;
use Kajona\System\System\Database;
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
        $events = $this->connection->getPArray('SELECT event_id, event_name, event_args FROM agp_system_events', [], 0, self::MAX_PREFETCH);

        foreach ($events as $event) {
            $eventName = $event['event_name'];
            $arguments = \json_decode($event['event_args'], true);

            // directly delete the event since otherwise this would block the queue in case the event throws an
            // unrecoverable error
            $this->connection->delete('agp_system_events', ['event_id' => $event['event_id']]);

            try {
                $this->eventDispatcher->notifyGenericListeners($eventName, $arguments);
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }

            // in case this workflow runs too long break and wait for the next execution
            if (time() - $startTime > self::MAX_EXECUTION) {
                break;
            }
        }
    }
}
