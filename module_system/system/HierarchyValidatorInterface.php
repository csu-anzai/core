<?php
/*"******************************************************************************************************
*   (c) 2010-2016 ARTEMEON                                                                              *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Interface for hierarchy validation
 *
 * @author stefan.meyer@artemeon.de
 * @module system
 */
interface HierarchyValidatorInterface
{

    /**
     * Method which checks if the given object can be moved within the hierarchy.
     *
     * @param $objObject
     * @return boolean - return true, if the node can be moved; return false, if the node should not be moved
     */
    public function isMovable(Root $objObject);

    /**
     * Method which checks if the given object can be moved only within a certain part of the hierarchy.
     * If this method return true,  a node below the given node CANNOT be moved outside the partial hierarchy
     * If this method return false, a node below the given node CAN    be moved outside the partial hierarchy
     *
     * Example 1: Move Node 'Node 2.1.1' below 'Node 1.1'
     * - Node 1 (hasParentPathCheck = true)
     *      - Node 1.1
     *          - Node 1.1.1
     *
     * - Node 2 (hasParentPathCheck = true)
     *      - Node 2.1
     *          - Node 2.1.1
     *
     * In this case 'Node 2.1.1' cannot be moved somewhere below 'Node 1' since one of the parent nodes of 'Node 2.1.1'
     * (in this case 'Node 2') has 'hasParentPathCheck = true'.
     * This means that if at least one parent node has this check active, the given node can only be moved within this part of the
     * hierarchy.
     *
     *
     * Example 2: Move Node 'Node 3.1.2' below 'Node 3'
     * - Node 3
     *      - Node 3.1 (hasParentPathCheck = true)
     *          - Node 3.1.1
     *          - Node 3.1.2
     *
     * In this case 'Node 3.1.2' cannot be moved below 'Node 3' since one of the parent nodes of 'Node 3.1.2'
     * (in this case 'Node 3.1') has 'hasParentPathCheck = true'.
     *
     * Example 3: Move Node 'Node 4.1.2' below 'Node 4'
     * - Node 4 (hasParentPathCheck = true)
     *      - Node 4.1
     *          - Node 4.1.1
     *          - Node 4.1.2
     *
     * In this case 'Node 4.1.2' can be moved below 'Node 4'.
     * 'Node 4' has 'hasParentPathCheck = true' but 'Node 4.1.2' is only moved within that part of the hierarchy.
     *
     * @param $objObject
     * @return boolean
     */
    public function isParentPathCheckActive(Root $objObject);

    /**
     * Method to indicate which child objects are allowed to be created below the given node.
     * Must return an array of classnames, e.g.
     *
     * return [
     *   ProzessverwaltungProzess::class,
     *   ProzessverwaltungProzesslink::class
     * ];
     *
     * If an empty array is returned no child nodes are allowed. This is default.
     * If null is returned any child nodes may be created below the node
     *
     * @param $objObject
     * @return array
     */
    public function getArrValidChildNodes(Root $objObject);

    /**
     * Validates if the current node can be put below the given new parent id
     *
     * @param $objObject
     * @param $strNewParentId
     * @return boolean
     */
    public function validateHierarchy(Root $objObject, $strNewParentId);
}