<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        *
********************************************************************************************************/

namespace Kajona\Dashboard\System;

use Kajona\Dashboard\Admin\Widgets\Adminwidget;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetInterface;
use Kajona\Dashboard\System\Filter\UserRootFilter;
use Kajona\System\System\AdminListableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Classloader;
use Kajona\System\System\FilterBase;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\OrmCondition;
use Kajona\System\System\OrmObjectlist;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemModule;

/**
 * Each user gets a root-node for its dashboard configs
 *
 * @package module_dashboard
 * @author stefan.idler@artemeon.de
 *
 * @targetTable agp_dashboard_root.root_id
 * @module dashboard
 * @moduleId _dashboard_module_id_
 */
class DashboardUserRoot extends Model implements ModelInterface
{
    /**
     * @var string
     * @tableColumn agp_dashboard_root.root_user
     * @tableColumnDatatype char20
     * @tableColumnIndex
     */
    private $strUser = "";

    /**
     * Fetches the root node per user if existing, otherwise a new one will be created
     * @param string $userId
     * @return DashboardUserRoot
     * @throws \Kajona\System\System\Exception
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public static function getOrCreateForUser(string $userId): DashboardUserRoot
    {
        $filter = new UserRootFilter();
        $filter->setStrUser($userId);

        $nodes = self::getObjectListFiltered($filter);

        if (empty($nodes)) {
            $node = new DashboardUserRoot();
            $node->setStrUser($userId);
            ServiceLifeCycleFactory::getLifeCycle($node)->update($node);
            $nodes[] = $node;
        }

        return current($nodes);
    }

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return $this->getStrUser();
    }

    /**
     * @return string
     */
    public function getStrUser()
    {
        return $this->strUser;
    }

    /**
     * @param string $strUser
     */
    public function setStrUser($strUser)
    {
        $this->strUser = $strUser;
    }

}
