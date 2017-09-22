<?php
/*"******************************************************************************************************
*   (c) 2010-2017 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace AGP\System\System\Validators;

use AGP\System\System\HierarchyValidatorInterface;
use Kajona\System\Admin\HierarchyValidatorFactory;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Root;
use Kajona\System\System\SystemModule;

/**
 * Base hierarchy validator
 *
 * @author stefan.meyer1@yahoo.de
 * @module system
 */
class HierarchyValidatorBase implements HierarchyValidatorInterface
{

    /**
     * Return true by default : All nodes are movable
     *
     * @inheritdoc
     */
    public function isMovable(Root $objObject)
    {
        return true;
    }

    /**
     * Return by default false: The node can be moved everywhere in the hierarchy
     *
     *
     * @inheritdoc
     */
    public function isParentPathCheckActive(Root $objObject)
    {
        return false;
    }

    /**
     * Returns by default an empty array: This means that no child nodes are allowed.
     *
     * @inheritdoc
     */
    public function getArrValidChildNodes(Root $objObject)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function validateHierarchy(Root $objObject, $strNewParentId)
    {
        $bitIsNew = !validateSystemid($objObject->getStrSystemid());

        if (!$bitIsNew) {//only check this if object already exists
            //1. Same parent id
            if ($objObject->getStrPrevId() === $strNewParentId) {
                return true;
            }

            //2. Check if movable
            if (!$this->isMovable($objObject)) {
                return false;
            }
        }

        $objNewObjectParent = Objectfactory::getInstance()->getObject($strNewParentId);
        if ($objNewObjectParent !== null) {
            //3. Check if given node is a valid child for the given new parent
            if (!$this->isValidChildNodeForParent($objObject, $objNewObjectParent)) {
                return false;
            }

            //4. Check if the given node has a valid parent
            if (!$this->isValidParentForChildNode($objObject, $objNewObjectParent)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Checks if the given node is a valid of the given parent.
     *
     * @param Root $objObject
     * @param Root $objNewObjectParent
     * @return bool
     */
    private function isValidChildNodeForParent(Root $objObject, Root $objNewObjectParent)
    {
        //Check if given node is a valid child for the given new parent
        $objValidatorParent = HierarchyValidatorFactory::newHierarchyValidator($objNewObjectParent);
        $arrValidChildren = $objValidatorParent->getArrValidChildNodes($objNewObjectParent);
        if (!in_array(get_class($objObject), $arrValidChildren)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current node has a valid parent node
     *
     * Determines if one of the parent nodes of the given node '$objObject' has isParentPathCheckActive set to true.
     *  If this is not the case, everything is ok -> return true
     *  If this is case it will checked, if the the new parent node '$objNewObjectParent' is somewhere within that path
     *
     * @param Root $objObject
     * @param Root $objNewObjectParent
     * @return bool
     */
    private function isValidParentForChildNode(Root $objObject, Root $objNewObjectParent)
    {
        //Check if one the parents of the given node '$objObject' has ParentPathCheckActive set
        $objParentNode = $this->getNodeWithParentPathCheckActive($objObject);
        if ($objParentNode !== null) {
            //Get path array of found node
            $arrPathIds = $objParentNode->getPathArray("", SystemModule::getModuleIdByNr($objParentNode->getIntModuleNr()));

            //Check, if the the new parent node '$objNewObjectParent' is somewhere within that path
            return in_array($objNewObjectParent->getStrSystemid(), $arrPathIds);
        }

        return true;
    }


    /**
     * Gets the first parent node which is no movable
     *
     * @param Root $objObject
     * @return \Kajona\System\System\Model|\Kajona\System\System\ModelInterface|null
     */
    private function getParentNodeMovable(Root $objObject)
    {
        $arrParentNodes = $objObject->getPathArray($objObject->getStrPrevId(), SystemModule::getModuleIdByNr($objObject->getIntModuleNr()));
        foreach ($arrParentNodes as $strParentId) {
            $objCurrParent = Objectfactory::getInstance()->getObject($strParentId);
            $objCurrValidatorParent = HierarchyValidatorFactory::newHierarchyValidator($objCurrParent);

            if (!$objCurrValidatorParent->isMovable($objCurrParent)) {
                return $objCurrParent;
            }
        }

        return null;
    }

    /**
     * Gets the first parent node which has ParentPathCheckActive set to true
     *
     * @param Root $objObject
     * @return \Kajona\System\System\Model|\Kajona\System\System\ModelInterface|null
     */
    private function getNodeWithParentPathCheckActive(Root $objObject)
    {
        $arrParentNodes = $objObject->getPathArray($objObject->getStrPrevId(), SystemModule::getModuleIdByNr($objObject->getIntModuleNr()));
        foreach ($arrParentNodes as $strParentId) {
            $objCurrParent = Objectfactory::getInstance()->getObject($strParentId);
            $objCurrValidatorParent = HierarchyValidatorFactory::newHierarchyValidator($objCurrParent);

            if ($objCurrValidatorParent->isParentPathCheckActive($objCurrParent)) {
                return $objCurrParent;
            }
        }

        return null;
    }
}
