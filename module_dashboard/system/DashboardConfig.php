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
use Kajona\System\System\AdminListableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Classloader;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\OrmCondition;
use Kajona\System\System\OrmObjectlist;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemModule;

/**
 * A dashboard config is the container for a set of dashboards
 *
 * @package module_dashboard
 * @author stefan.idler@artemeon.de
 *
 * @targetTable agp_dashboard_cfg.cfg_id
 * @module dashboard
 * @moduleId _dashboard_module_id_
 */
class DashboardConfig extends Model implements ModelInterface, AdminListableInterface
{

    /**
     * @var string
     * @tableColumn agp_dashboard_cfg.cfg_title
     * @tableColumnDatatype char254
     */
    private $strTitle = "";

    /**
     * @var bool
     * @tableColumn agp_dashboard_cfg.cfg_default
     * @tableColumnDatatype char20
     */
    private $bitDefault = false;


    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return $this->getStrTitle();
    }

    /**
     * @inheritDoc
     */
    public function getStrIcon()
    {
        return "icon_dot";
    }

    /**
     * @inheritDoc
     */
    public function getStrAdditionalInfo()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function getStrLongDescription()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getStrTitle()
    {
        return $this->strTitle;
    }

    /**
     * @param string $strTitle
     */
    public function setStrTitle($strTitle)
    {
        $this->strTitle = $strTitle;
    }

    /**
     * @return bool
     */
    public function isBitDefault()
    {
        return $this->bitDefault;
    }

    /**
     * @param bool $bitDefault
     */
    public function setBitDefault($bitDefault)
    {
        $this->bitDefault = $bitDefault;
    }

}
