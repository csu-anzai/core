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

use Kajona\System\System\Root;
use Kajona\System\System\UserGroup;

/**
 * The permissions handler is a service which handles rights on an object. It is invoked if we create or update a
 * model. Since it is a service it is also possible to customize the implementation for a project. You can specify a
 * permission handler on the model through the @permissionHandler annotation. Basically a handler follows two basic
 * concepts:
 *
 * - Roles
 * A role is a string identifier which can be resolved to multiple user groups. We are working with such roles since it
 * is often needed to _not_ simply set a fix user group id but resolve a user group based on a specific property or
 * assigned OE from the model. The handler contains all available group types for the model and can resolve such a role
 * to actual user group objects
 *
 * - Right handling
 * If a model is created or changed the right handler has the chance to also adjust the rights of the model or also any
 * other assigned models. Note it gets only invoked in case you use the life cycle service
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface PermissionHandlerInterface
{
    const PERMISSION_HANDLER_ANNOTATION = '@permissionHandler';

    /**
     * Returns all available roles for this handler
     *
     * @return array
     */
    public function getRoles();

    /**
     * Returns an array of user groups or an empty array. The key of the array must contain the systemid of the group
     * this simplfies handling or merging multiple groups
     *
     * @param Root $objRecord
     * @param string $strRole
     * @return UserGroup[]
     */
    public function getGroupsByRole(Root $objRecord, $strRole);

    /**
     * Returns all rights which are available for a specific role
     *
     * @param string $strRole
     * @return array
     */
    public function getRoleRights($strRole);

    /**
     * Sets the initial rights of an record
     *
     * @param Root $objRecord
     * @return void
     */
    public function onCreate(Root $objRecord);

    /**
     * Sets rights of an record on update
     *
     * @param Root $objOldRecord
     * @param Root $objNewRecord
     * @return void
     */
    public function onUpdate(Root $objOldRecord, Root $objNewRecord);
}
