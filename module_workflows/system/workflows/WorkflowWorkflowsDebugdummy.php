<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Workflows\System\Workflows;

use Kajona\System\System\Date;
use Kajona\Workflows\System\WorkflowsHandlerInterface;
use Kajona\Workflows\System\WorkflowsWorkflow;

/**
 * Workflow to create a dbdump in a regular interval, by default configured for 24h
 *
 * @package module_workflows
 */
class WorkflowWorkflowsDebugdummy implements WorkflowsHandlerInterface
{

    private $intSleep = 120;

    /**
     * @var WorkflowsWorkflow
     */
    private $objWorkflow = null;

    /**
     * @inheritdoc
     */
    public function getConfigValueNames()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function setConfigValues($strVal1, $strVal2, $strVal3)
    {
    }

    /**
     * @inheritdoc
     */
    public function getDefaultValues()
    {
        return array();
    }

    public function setObjWorkflow($objWorkflow)
    {
        $this->objWorkflow = $objWorkflow;
    }

    public function getStrName()
    {
        return "Debugging Workflow";
    }


    public function execute()
    {
        sleep($this->intSleep);
        //trigger again
        return false;
    }

    public function onDelete()
    {
    }


    public function schedule()
    {
        $this->objWorkflow->setObjTriggerdate(new Date());
    }

    public function getUserInterface()
    {
    }

    public function processUserInput($arrParams)
    {
        return;
    }

    public function providesUserInterface()
    {
        return false;
    }


}
