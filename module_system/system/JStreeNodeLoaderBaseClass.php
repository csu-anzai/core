<?php
/*"******************************************************************************************************
*   (c) 2010-2018 ARTEMEON                                                                              *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                         *
********************************************************************************************************/

namespace Kajona\System\System;

/**
 * Base class for loading tree nodes.
 * Class provides basic implementation for getNodesByPath.
 *
 * @package module_system
 * @author andrii.konoval@artemeon.de
 *
 */
abstract class JStreeNodeLoaderBaseClass implements JStreeNodeLoaderInterface
{

    /**
     * @inheritdoc
     */
    public function getNodesByPath($arrSystemIdPath)
    {
        if(empty($arrSystemIdPath)) {
            return true;
        }

        $strSystemId = array_shift($arrSystemIdPath);
        $arrChildren = $this->getChildNodes($strSystemId);

        $strSubId = array_key_exists(0, $arrSystemIdPath) ? $arrSystemIdPath[0] : null;
        foreach($arrChildren as $objChildNode) {

            if($strSubId !== null && $objChildNode->getStrId() == $strSubId) {
                $objChildNode->addStateAttr(SystemJSTreeNode::STR_NODE_STATE_OPENED, true);

                $arrSubchildNodes = $this->getNodesByPath($arrSystemIdPath);
                $objChildNode->setArrChildren($arrSubchildNodes);
            }
        }

        return $arrChildren;
    }
}