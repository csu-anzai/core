<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Workflows\Tests;

use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\Tests\Testbase;
use Kajona\Workflows\System\WorkflowsWorkflow;

class WorkflowTest extends Testbase
{

    /**
     * Tests method getWorkflowsForSystemid with newly created workflow objects
     */
    public function test_getWorkflowsForSystemid_1()
    {

        //1 Init settings
        $strSystemId1 = generateSystemid();
        $strSystemId2 = generateSystemid();
        $strWorkflowClass_1 = "Kajona\\Workflows\\System\\Workflows\\Test";
        $strWorkflowClass_2 = "Kajona\\Workflows\\System\\Workflows\\Test_2";
        $arrWorkflowsClasses =
            array(
                array("class" => $strWorkflowClass_1, "systemid" => $strSystemId1, "amount" => 5),
                array("class" => $strWorkflowClass_1, "systemid" => $strSystemId2, "amount" => 5),
                array("class" => $strWorkflowClass_2, "systemid" => $strSystemId2, "amount" => 23)
            );


        //2. Create the workflow objects
        $arrCreatedWorkflows = array();
        foreach ($arrWorkflowsClasses as $arrInfo) {
            for ($intI = 0; $intI < $arrInfo["amount"]; $intI++) {
                $objWorkflow = new WorkflowsWorkflow();
                $objWorkflow->setStrClass($arrInfo["class"]);
                $objWorkflow->setStrAffectedSystemid($arrInfo["systemid"]);
                ServiceLifeCycleFactory::getLifeCycle(get_class($objWorkflow))->update($objWorkflow);
                $arrCreatedWorkflows[] = $objWorkflow;
            }
        }


        $this->flushDBCache();

        //3. Assert number of workflows
        foreach ($arrWorkflowsClasses as $arrInfo) {
            $arrWorkflows = WorkflowsWorkflow::getWorkflowsForSystemid($arrInfo["systemid"], false, $arrInfo["class"]);
            $this->assertEquals(count($arrWorkflows), $arrInfo["amount"]);

            $arrWorkflows = WorkflowsWorkflow::getWorkflowsForSystemid($arrInfo["systemid"], false, array($arrInfo["class"]));
            $this->assertEquals(count($arrWorkflows), $arrInfo["amount"]);
        }

        $arrWorkflows = WorkflowsWorkflow::getWorkflowsForSystemid($strSystemId1, false, array($strWorkflowClass_1));
        $this->assertEquals(count($arrWorkflows), 5);
        $arrWorkflows = WorkflowsWorkflow::getWorkflowsForSystemid($strSystemId2, false, array(
            $strWorkflowClass_1,
            $strWorkflowClass_2
        ));
        $this->assertEquals(count($arrWorkflows), 28);


        //4. Assert workflow by class
        $arrWorkflows = WorkflowsWorkflow::getWorkflowsForClass($strWorkflowClass_1, false);
        $this->assertCount(10, $arrWorkflows);
        $arrWorkflows = WorkflowsWorkflow::getWorkflowsForClass($strWorkflowClass_2, false);
        $this->assertCount(23, $arrWorkflows);


        //4. Delete created workflow objects
        /** @var WorkflowsWorkflow $objWorkflow */
        foreach ($arrCreatedWorkflows as $objWorkflow) {
            $objWorkflow->deleteObjectFromDatabase();
        }

        $this->resetCaches();
    }


    public function test_getWorkflowsForSystemid()
    {
        //execute test case with invalid systemid
        $arrReturn = WorkflowsWorkflow::getWorkflowsForSystemid("ddd", false, array("Kajona\\Workflows\\System\\Workflows\\WorkflowWorkflowsMessagesummary"));
        $this->assertEquals(0, count($arrReturn));
    }
}