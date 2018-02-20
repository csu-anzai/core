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
 * Single action to be used in combination with the permission batch manager
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 7.0
 *
 */
class AddPermissionToGroup implements PermissionActionInterface
{
    private $systemid = "";
    private $groupId = "";
    private $permission = "";

    /**
     * AddPermissionToGroup constructor.
     * @param $strSystemid
     * @param $strGroupId
     * @param $strPermission
     */
    public function __construct(string $strSystemid, string $strGroupId, string $strPermission)
    {
        $this->systemid = $strSystemid;
        $this->groupId = $strGroupId;
        $this->permission = $strPermission;
    }

    /**
     * @inheritdoc
     */
    public function applyAction($permissions): array
    {
        if (!in_array($this->getGroupId(), $permissions[$this->permission])) {
            $permissions[$this->permission][] = $this->getGroupId();
        }
        return $permissions;
    }


    /**
     * @return string
     */
    public function getSystemid(): string
    {
        return $this->systemid;
    }

    /**
     * @param string $systemid
     */
    public function setSystemid(string $systemid)
    {
        $this->systemid = $systemid;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     */
    public function setGroupId(string $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     */
    public function setPermission(string $permission)
    {
        $this->permission = $permission;
    }
}
