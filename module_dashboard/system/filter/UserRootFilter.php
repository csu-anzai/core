<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Dashboard\System\Filter;

use Kajona\System\System\FilterBase;

/**
 * Filter for a contracts servicer
 *
 * @module dashboard
 * @moduleId _dashboard_module_id_
 */
class UserRootFilter extends FilterBase
{

    /**
     * @var string
     * @tableColumn agp_dashboard_root.root_user
     * @tableColumnDatatype char20
     */
    private $strUser = "";

    /**
     * @return string
     */
    public function getStrUser(): string
    {
        return $this->strUser;
    }

    /**
     * @param string $strUser
     */
    public function setStrUser(string $strUser)
    {
        $this->strUser = $strUser;
    }
}
