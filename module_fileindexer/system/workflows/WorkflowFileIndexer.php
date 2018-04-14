<?php
/*"******************************************************************************************************
*   (c) 2010-2016 ARTEMEON                                                                              *
********************************************************************************************************/

namespace Kajona\Fileindexer\System\Workflows;

use Kajona\Fileindexer\System\Indexer;
use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\Mediamanager\System\MediamanagerRepoFilter;
use Kajona\System\System\Carrier;
use Kajona\System\System\Date;
use Kajona\Workflows\System\WorkflowsHandlerInterface;
use Kajona\Workflows\System\WorkflowsWorkflow;

/**
 * WorkflowFileIndexer
 *
 * @package module_fileindexer
 */
class WorkflowFileIndexer implements WorkflowsHandlerInterface
{
    private $intDelay = 3;

    /**
     * @inject fileindexer_indexer
     * @var Indexer
     */
    protected $objIndexer;

    /**
     * @var WorkflowsWorkflow
     */
    private $objWorkflow = null;

    /**
     * @see WorkflowsHandlerInterface::getConfigValueNames()
     */
    public function getConfigValueNames()
    {
        return array(
            Carrier::getInstance()->getObjLang()->getLang("workflow_file_indexer_delay", "fileindexer")
        );
    }

    /**
     * @see WorkflowsHandlerInterface::setConfigValues()
     *
     * @param string $strVal1
     * @param string $strVal2
     * @param string $strVal3
     */
    public function setConfigValues($strVal1, $strVal2, $strVal3)
    {
        $this->intDelay = (int)$strVal1;
    }

    /**
     * @see WorkflowsHandlerInterface::getDefaultValues()
     */
    public function getDefaultValues()
    {
        return array(3);
    }

    public function setObjWorkflow($objWorkflow)
    {
        $this->objWorkflow = $objWorkflow;
    }

    public function getStrName()
    {
        return Carrier::getInstance()->getObjLang()->getLang("workflow_file_indexer", "fileindexer");
    }

    public function execute()
    {
        $objFilter = new MediamanagerRepoFilter();
        $objFilter->setBitSearchIndex(true);
        $arrRepos = MediamanagerRepo::getObjectListFiltered($objFilter);

        foreach ($arrRepos as $objRepo) {
            $this->objIndexer->index($objRepo);
        }

        //trigger again
        return false;
    }

    public function onDelete()
    {
    }

    public function schedule()
    {
        $objTriggerdate = new Date(time() + (60 * $this->intDelay));
        $this->objWorkflow->setTriggerdate($objTriggerdate);
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
