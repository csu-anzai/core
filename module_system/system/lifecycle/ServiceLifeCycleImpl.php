<?php

namespace Kajona\System\System\Lifecycle;

use AGP\Prozessverwaltung\System\ProzessverwaltungObjectBase;
use Kajona\System\System\Carrier;
use Kajona\System\System\Database;
use Kajona\System\System\Permissions\PermissionHandlerInterface;
use Kajona\System\System\RedirectException;
use Kajona\System\System\Reflection;
use Kajona\System\System\Root;

/**
 * ServiceLifeCycleImpl
 *
 * @package Kajona\System\System
 * @author christoph.kappestein@gmail.com
 * @since 7.0
 */
class ServiceLifeCycleImpl implements ServiceLifeCycleInterface
{
    /**
     * @inheritdoc
     */
    public function update(Root $objModel, $strPrevId = false)
    {
        Database::getInstance()->transactionBegin();

        try {
            $bitIsNew  = !validateSystemid($objModel->getSystemid());
            $bitReturn = $objModel->updateObjectToDb($strPrevId);

            if (!$bitReturn) {
                throw new ServiceLifeCycleUpdateException("Error updating object ".strip_tags($objModel->getStrDisplayName()), $objModel->getSystemid());
            }

            // call permission handler if available
            if ($objModel instanceof ProzessverwaltungObjectBase && !$objModel->isObjectdesignerTemplate()) {
                $this->invokePermissionHandler($objModel, $bitIsNew);
            }

            Database::getInstance()->transactionCommit();
        } catch (RedirectException $objE) {
            Database::getInstance()->transactionCommit();

            throw $objE;
        } catch (\Exception $objE) {
            Database::getInstance()->transactionRollback();

            throw $objE;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(Root $objModel)
    {
        $bitReturn = $objModel->deleteObject();

        if (!$bitReturn) {
            throw new ServiceLifeCycleLogicDeleteException("Error logic deleting object ".strip_tags($objModel->getStrDisplayName()), $objModel->getSystemid());
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteObjectFromDatabase(Root $objModel)
    {
        $bitReturn = $objModel->deleteObjectFromDatabase();

        if (!$bitReturn) {
            throw new ServiceLifeCycleDeleteException("Error deleting object ".strip_tags($objModel->getStrDisplayName()), $objModel->getSystemid());
        }
    }

    /**
     * @inheritdoc
     */
    public function restore(Root $objModel)
    {
        $bitReturn = $objModel->restoreObject();

        if (!$bitReturn) {
            throw new ServiceLifeCycleRestoreException("Error restoring object ".strip_tags($objModel->getStrDisplayName()), $objModel->getSystemid());
        }
    }

    /**
     * @inheritdoc
     */
    public function copy(Root $objModel, $strNewPrevid = false, $bitChangeTitle = true, $bitCopyChilds = true)
    {
        $bitReturn = $objModel->copyObject($strNewPrevid ?: "", $bitChangeTitle, $bitCopyChilds);

        if (!$bitReturn) {
            throw new ServiceLifeCycleCopyException("Error creating a copy of object ".strip_tags($objModel->getStrDisplayName()), $objModel->getSystemid());
        }

        return $objModel;
    }

    /**
     * @param Root $objModel
     * @param bool $bitIsNew
     * @throws \Kajona\System\System\Exception
     */
    private function invokePermissionHandler(Root $objModel, $bitIsNew)
    {
        // check whether the model has a permission handler
        $objReflection = new Reflection($objModel);
        $arrHandler = $objReflection->getAnnotationValuesFromClass(PermissionHandlerInterface::PERMISSION_HANDLER_ANNOTATION);

        if (!empty($arrHandler)) {
            $strHandler = reset($arrHandler);
            $objPermissionHandler = Carrier::getInstance()->getContainer()->offsetGet($strHandler);

            if ($objPermissionHandler instanceof PermissionHandlerInterface) {
                if ($bitIsNew) {
                    $objPermissionHandler->onInitialize($objModel);
                } else {
                    $objPermissionHandler->onUpdate($objModel);
                }
            }
        }
    }
}
