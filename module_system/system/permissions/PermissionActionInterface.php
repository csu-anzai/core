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

/**
 * marker for type-checks
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 7.0
 *
 */
interface PermissionActionInterface
{
    /**
     * Returns the id of the matched record
     *
     * @return string
     */
    public function getSystemid(): string;

    /**
     * Returns the priority of this action
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Applies the action to the set of permissions.
     *
     * @param array $permissions
     * @return array
     * @see Rights::getArrayRights()
     */
    public function applyAction(array $permissions): array;
}
