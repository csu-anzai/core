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
 * Removes a specific group
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class RemoveGroup implements PermissionActionInterface
{
    /**
     * @var string
     */
    private $systemId = "";

    /**
     * @var string
     */
    private $groupId = "";

    /**
     * @param string $systemId
     * @param string $groupId
     */
    public function __construct(string $systemId, string $groupId)
    {
        $this->systemId = $systemId;
        $this->groupId = $groupId;
    }

    public function getSystemid(): string
    {
        return $this->systemId;
    }

    /**
     * @inheritdoc
     */
    public function applyAction(array $permissions): array
    {
        return array_map(function($rights){
            return array_values(array_filter($rights, function($groupId){
                return $groupId != $this->groupId;
            }));
        }, $permissions);
    }
}
