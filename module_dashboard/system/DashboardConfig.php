<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Dashboard\System;

use Kajona\Dashboard\System\Filter\DashboardConfigFilter;
use Kajona\System\System\AdminListableInterface;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;

/**
 * A dashboard config is the container for a set of dashboards
 *
 * @package module_dashboard
 * @author stefan.idler@artemeon.de
 *
 * @targetTable agp_dashboard_cfg.cfg_id
 * @module dashboard
 * @moduleId _dashboard_module_id_
 *
 * @lifeCycleService dashboard_life_cycle_config
 */
class DashboardConfig extends Model implements ModelInterface, AdminListableInterface
{

    /**
     * @var string
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     * @fieldMandatory
     * @fieldLabel commons_title
     * @tableColumn agp_dashboard_cfg.cfg_title
     * @tableColumnDatatype char254
     */
    private $strTitle = "";

    /**
     * @var bool
     * @fieldType Kajona\System\Admin\Formentries\FormentryYesno
     * @tableColumn agp_dashboard_cfg.cfg_default
     * @tableColumnDatatype char20
     * @listOrder DESC
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
        return $this->bitDefault ? $this->getLang("form_dashboard_default") : "";
    }

    /**
     * @inheritDoc
     */
    public function getStrLongDescription()
    {
        return "";
    }

    /**
     * @param string $rootId - The dashboard user root id
     * @param string $title
     * @return DashboardConfig|null
     */
    public static function getByTitle(string $rootId, string $title)
    {
        $filter = new DashboardConfigFilter();
        $filter->setStrRootId($rootId);
        $filter->setStrTitle($title);

        $result = self::getObjectListFiltered($filter);
        return $result[0] ?? null;
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
    public function getBitDefault()
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
