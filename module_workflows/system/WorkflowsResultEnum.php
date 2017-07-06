<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Workflows\System;

use Kajona\System\System\EnumBase;


/**
 * Enum representing the execution states of a handler, used in the stats part
 *
 * @package module_workflows
 * @author sidler@mulchprod.de
 *
 * @method static WorkflowsResultEnum INACTIVE()
 * @method static WorkflowsResultEnum LOCKED()
 * @method static WorkflowsResultEnum PROCESSED_BY_OTHER_THREAD()
 * @method static WorkflowsResultEnum EXECUTE_FINISHED()
 * @method static WorkflowsResultEnum EXECUTE_SCHEDULED()
 */
class WorkflowsResultEnum extends EnumBase
{

    /**
     * Return the array of possible, so allowed values for the current enum
     *
     * @return string[]
     */
    protected function getArrValues()
    {
        return ["INACTIVE", "LOCKED", "EXECUTE_FINISHED", "EXECUTE_SCHEDULED", "PROCESSED_BY_OTHER_THREAD"];
    }
}
