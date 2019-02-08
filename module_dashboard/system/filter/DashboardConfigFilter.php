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
     * @var string
     * @tableColumn agp_dashboard_cfg.cfg_title
     * @tableColumnDatatype char20
     */
    private $strTitle = "";

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
