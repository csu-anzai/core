<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Flow\System;

use Kajona\System\System\Model;

/**
 * Class which contains all available event identifiers
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface FlowEventidentifier
{
    /**
     * Callback method in case a transition was executed. The event gets triggered after every action and the handler
     * code was executed. Note the event is triggered inside a transaction this means if the event throws an error the
     * complete status change gets reverted
     *
     * @param Model $object
     * @param FlowTransition $transition
     *
     * @return bool
     * @since 7.0
     */
    const EVENT_TRANSITION_EXECUTED = "flow.transition.executed";
}
