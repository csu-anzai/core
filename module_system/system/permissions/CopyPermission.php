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
 * Copies specific permissions from a foreign record
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class CopyPermission implements PermissionActionInterface
{
    /**
     * @var Rights
     */
    private $rights;

    /**
     * @var string
     */
    private $systemId = "";

    /**
     * @var string
     */
    private $foreignId = "";

    /**
     * @var string
     */
    private $permission = "";

    /**
     * @param string $systemId
     * @param string $foreignId
     * @param string $permission
     */
    public function __construct(Rights $rights, string $systemId, string $foreignId, string $permission)
    {
        $this->rights = $rights;
        $this->systemId = $systemId;
        $this->foreignId = $foreignId;
        $this->permission = $permission;
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
        $inheritRights = $this->rights->getArrayRights($this->foreignId, $this->permission);
        $result = [];

        foreach ($permissions as $right => $groupIds) {
            if (isset($inheritRights[$right])) {
                $result[$right] = $inheritRights[$right];
            } else {
                $result[$right] = $groupIds;
            }
        }

        return $result;
    }
}
