<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Statustransition\System;

use Kajona\System\System\Database;
use Kajona\System\System\Exception;
use Kajona\System\System\Model;

/**
 * StatustransitionFlowChoiceInterface
 *
 * @author christoph.kappestein@artemeon.de
 * @module statustransition
 */
interface StatustransitionFlowChoiceInterface
{
    /**
     * Returns the fitting statustransition flow for this object
     *
     * @return StatustransitionFlow
     */
    public function getStatusTransitionFlow();
}
