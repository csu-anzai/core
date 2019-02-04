<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Dashboard\System\Filter;

use Kajona\System\System\FilterBase;

/**
 * Filter for a dashboard config
 *
 * @module dashboard
 * @moduleId _dashboard_module_id_
 */
class DashboardConfigFilter extends FilterBase
{
    /**
     * The dashboard user root id
     *
     * @var string
     * @tableColumn agp_system.system_prev_id
     * @tableColumnDatatype char20
     */
    private $strRootId = "";

    /**
     * @var string
     * @tableColumn agp_dashboard_cfg.cfg_title
     * @tableColumnDatatype char20
     */
    private $strTitle = "";

    /**
     * @return string
     */
    public function getStrRootId(): string
    {
        return $this->strRootId;
    }

    /**
     * @param string $strRootId
     */
    public function setStrRootId(string $strRootId)
    {
        $this->strRootId = $strRootId;
    }

    /**
     * @return string
     */
    public function getStrTitle(): string
    {
        return $this->strTitle;
    }

    /**
     * @param string $strTitle
     */
    public function setStrTitle(string $strTitle)
    {
        $this->strTitle = $strTitle;
    }
}
