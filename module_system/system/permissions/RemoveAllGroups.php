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

use Kajona\System\System\SystemSetting;

/**
 * Removes a all groups from every permission
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class RemoveAllGroups implements PermissionActionInterface
{
    /**
     * @var string
     */
    private $systemId = "";

    /**
     * @param string $systemId
     * @param string $groupId
     */
    public function __construct(string $systemId)
    {
        $this->systemId = $systemId;
    }

    /**
     * @inheritdoc
     */
    public function getSystemid(): string
    {
        return $this->systemId;
    }

    /**
     * This action must be executed always at fist to reset the rights
     */
    public function getPriority(): int
    {
        return -64;
    }

    /**
     * @inheritdoc
     */
    public function applyAction(array $permissions): array
    {
        return array_map(function($rights){
            $adminGroupId = SystemSetting::getConfigValue("_admins_group_id_");
            return [$adminGroupId];
        }, $permissions);
    }
}
