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
 * Replaces a specific group with another group in all permissions
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class ReplaceGroup implements PermissionActionInterface
{
    /**
     * @var string
     */
    private $systemId = "";

    /**
     * @var string
     */
    private $oldGroupId = "";

    /**
     * @var string
     */
    private $newGroupId = "";

    /**
     * @param string $systemId
     * @param string $oldGroupId
     * @param string $newGroupId
     */
    public function __construct(string $systemId, string $oldGroupId, string $newGroupId)
    {
        $this->systemId = $systemId;
        $this->oldGroupId = $oldGroupId;
        $this->newGroupId = $newGroupId;
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
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function applyAction(array $permissions): array
    {
        return array_map(function($rights){
            return array_map(function($groupId){
                if ($groupId == $this->oldGroupId) {
                    return $this->newGroupId;
                } else {
                    return $groupId;
                }
            }, $rights);
        }, $permissions);
    }
}
