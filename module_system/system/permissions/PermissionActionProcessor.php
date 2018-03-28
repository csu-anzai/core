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

use Kajona\System\System\Rights;

/**
 * Manager to process a set of permission operations
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 7.0
 *
 */
class PermissionActionProcessor
{
    /** @var PermissionActionInterface[][]  */
    private $actions = [];

    /** @var Rights */
    private $rights;

    /**
     * PermissionActionProcessor constructor.
     * @param Rights $rights
     */
    public function __construct(Rights $rights)
    {
        $this->rights = $rights;
    }


    /**
     * Applies the set of changes. Detects if a permission update is required.
     * Returns true in case a record was updated, false in case no update was required.
     *
     * @return bool
     * @throws \Kajona\System\System\Exception
     */
    public function applyActions(): bool
    {
        $updated = false;
        //fetch the old permissions
        foreach ($this->actions as $systemid => $actions) {
            if (empty($actions)) {
                continue;
            }

            // sort all actions for this systemid after priority
            $sortedActions = $actions;
            usort($sortedActions, function (PermissionActionInterface $a, PermissionActionInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            });

            $permissionRow = $this->rights->getArrayRights($systemid);
            if (array_key_exists(Rights::$STR_RIGHT_INHERIT, $permissionRow)) {
                unset($permissionRow[Rights::$STR_RIGHT_INHERIT]);
            }
            $oldPermissionRow = $permissionRow;

            foreach ($sortedActions as $singleAction) {
                /** @var PermissionActionInterface $singleAction */
                $permissionRow = $singleAction->applyAction($permissionRow);
            }

            //check if an update is required
            if ($this->hasChanged($permissionRow, $oldPermissionRow)) {
                $permissionRow[Rights::$STR_RIGHT_INHERIT] = 0;
                $this->rights->setRights($this->rights->convertSystemidArrayToShortIdString($permissionRow), $systemid);
                $updated = true;
            }
        }
        return $updated;
    }

    /**
     * Diffs two two-dim arrays in order to detect changes.
     *
     * @param array $baseArray
     * @param array $compareArray
     * @return bool
     */
    private function hasChanged(array $baseArray, array $compareArray): bool
    {
        foreach ($baseArray as $permission => $baseValues) {
            if ($permission == Rights::$STR_RIGHT_INHERIT) {
                continue;
            }
            $compareValues = $compareArray[$permission];
            if (count($baseValues) != count($compareValues)) {
                return true;
            }
            if (count(array_diff($compareValues, $baseValues)) > 0) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param PermissionActionInterface $operation
     */
    public function addAction(PermissionActionInterface $operation)
    {
        if (empty($this->actions[$operation->getSystemid()])) {
            $this->actions[$operation->getSystemid()] = [];
        }

        $this->actions[$operation->getSystemid()][] = $operation;
    }

}
