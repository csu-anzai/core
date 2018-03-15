<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                        *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System\Permissions;

use Kajona\Flow\System\FlowManager;
use Kajona\Flow\System\FlowStatus;
use Kajona\System\System\Exception;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Rights;
use Kajona\System\System\Root;

/**
 * PermissionHandlerAbstract
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
abstract class PermissionHandlerAbstract implements PermissionHandlerInterface
{
    /**
     * @var Objectfactory
     */
    protected $objFactory;

    /**
     * @var Rights
     */
    protected $objRights;

    /**
     * @var FlowManager
     */
    protected $objFlowManager;

    /**
     * @param Objectfactory $objFactory
     * @param Rights $objRights
     * @param FlowManager $objFlowManager
     */
    public function __construct(Objectfactory $objFactory, Rights $objRights, FlowManager $objFlowManager)
    {
        $this->objFactory = $objFactory;
        $this->objRights = $objRights;
        $this->objFlowManager = $objFlowManager;
    }

    /**
     * @inheritdoc
     */
    public function onCreate(Root $objRecord)
    {
        $this->calculatePermissions($objRecord);
    }

    /**
     * @inheritdoc
     */
    public function onUpdate(Root $objOldRecord, Root $objNewRecord)
    {
        // if nothing has changed we also dont need to set the rights
        if (!$this->hasChanged($objOldRecord, $objNewRecord)) {
            return;
        }

        $this->calculatePermissions($objNewRecord);
    }

    /**
     * @inheritdoc
     */
    public function calculatePermissions(Root $objRecord)
    {
        $objStatus = $this->objFlowManager->getCurrentStepForModel($objRecord);

        if ($objStatus instanceof FlowStatus) {
            $objProcessor = new PermissionActionProcessor($this->objRights);

            $this->setRights($objStatus, $objRecord, $objProcessor);

            $objProcessor->applyActions();
        }
    }

    /**
     * This method must return whether a property has changed so that we need to set the rights i.e. in case the status
     * or a OE has changed
     *
     * @param Root $objOldRecord
     * @param Root $objNewRecord
     * @return bool
     */
    protected function hasChanged(Root $objOldRecord, Root $objNewRecord)
    {
        return $objOldRecord->getIntRecordStatus() != $objNewRecord->getIntRecordStatus();
    }

    /**
     * Method which adds specific right actions to the provided permission action processor
     *
     * @param FlowStatus $objStatus
     * @param Root $objRecord
     * @param PermissionActionProcessor $objProcessor
     * @throws Exception
     */
    protected function setRights(FlowStatus $objStatus, Root $objRecord, PermissionActionProcessor $objProcessor)
    {
        $arrRoles = $this->getRoles();
        $objProcessor->addAction(new RemoveAllGroups($objRecord->getSystemid()));

        foreach ($arrRoles as $strRole) {
            $arrGroups = $this->getGroupsByRole($objRecord, $strRole);
            $arrRights = $objStatus->getRightsForRole($strRole);

            foreach ($arrGroups as $objGroup) {
                foreach ($arrRights as $strRight) {
                    $objProcessor->addAction(new AddPermissionToGroup($objRecord->getSystemid(), $objGroup->getSystemid(), $strRight));
                }
            }
        }
    }
}
