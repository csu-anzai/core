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
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface PermissionHandlerInterface
{
    const PERMISSION_HANDLER_ANNOTATION = '@permissionHandler';

    /**
     * Returns all available groups types for this handler
     *
     * @return array
     */
    public function getGroupTypes();

    /**
     * Resolve the group type to an acutal user group
     *
     * @param Root $objRecord
     * @param string $strGroupType
     * @return UserGroup|null
     */
    public function getGroup(Root $objRecord, $strGroupType);

    /**
     * Sets the initial rights of an record
     *
     * @param Root $objRecord
     * @return void
     */
    public function onInitialize(Root $objRecord);

    /**
     * Sets rights of an record on update
     *
     * @param Root $objRecord
     * @return void
     */
    public function onUpdate(Root $objRecord);
}
