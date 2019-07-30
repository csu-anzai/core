<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\System\Messagequeue;

use Kajona\System\System\Database;

/**
 * Producer
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.2
 */
class Producer
{
    /**
     * @var Database
     */
    private $connection;

    /**
     * @param Database $connection
     */
    public function __construct(Database $connection)
    {
        $this->connection = $connection;
    }

    public function dispatch(Event $event): void
    {
        $this->connection->insert('agp_system_events', [
            'event_id' => generateSystemid(),
            'event_name' => $event->getName(),
            'event_args' => \json_encode($event->getArguments()),
        ]);
    }
}
