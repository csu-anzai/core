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
        $objStatus = $this->objFlowManager->getCurrentStepForModel($objRecord);

        if ($objStatus instanceof FlowStatus) {
            $this->setRights($objStatus, $objRecord);
        }
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

        $objStatus = $this->objFlowManager->getCurrentStepForModel($objNewRecord);

        if ($objStatus instanceof FlowStatus) {
            $this->setRights($objStatus, $objNewRecord);
        }
    }

    /**
     * This method must return whether a property has changed so that we need to set the rights i.e. in case a OE has
     * changed
     *
     * @param Root $objOldRecord
     * @param Root $objNewRecord
     * @return bool
     */
    abstract protected function hasChanged(Root $objOldRecord, Root $objNewRecord);

    /**
     * @param FlowStatus $objStatus
     * @param Root $objRecord
     * @throws Exception
     */
    protected function setRights(FlowStatus $objStatus, Root $objRecord)
    {
        $arrRoles = $this->getRoles();
        $objProcessor = new PermissionActionProcessor($this->objRights);

        foreach ($arrRoles as $strRole) {
            $arrGroups = $this->getGroupsByRole($objRecord, $strRole);
            $arrRights = $objStatus->getRightsForRole($strRole);

            foreach ($arrRights as $strRight => $strValue) {
                $arrGroupIds = [];
                foreach ($arrGroups as $objGroup) {
                    $arrGroupIds[] = $objGroup->getSystemid();
                }

                $objProcessor->addAction(new SetGroupsToPermission($objRecord->getSystemid(), $strRight, $arrGroupIds));
            }
        }

        $objProcessor->applyActions();
    }
}
