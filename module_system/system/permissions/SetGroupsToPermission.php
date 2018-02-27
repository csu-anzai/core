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
 * Sets all groups for a specific permission and overwrites all existing entries
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class SetGroupsToPermission implements PermissionActionInterface
{
    /**
     * @var string
     */
    private $systemId = "";

    /**
     * @var string
     */
    private $groupIds = [];

    /**
     * @var string
     */
    private $permission = "";

    /**
     * @param string $systemId
     * @param string $permission
     * @param string $groupIds
     */
    public function __construct(string $systemId, string $permission, array $groupIds)
    {
        $this->systemId = $systemId;
        $this->permission = $permission;
        $this->groupIds = $groupIds;
    }

    /**
     * @inheritdoc
     */
    public function getSystemid(): string
    {
        return $this->systemId;
    }

    /**
     * @inheritdoc
     */
    public function applyAction(array $permissions): array
    {
        $permissions[$this->permission] = $this->groupIds;
        return $permissions;
    }
}
