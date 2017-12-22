<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Flow\System;

/**
 * A model can implement this interface to provide a special right handling. By default
 * we check the edit right
 *
 * @author christoph.kappestein@artemeon.de
 * @module flow
 */
interface FlowModelRightInterface
{
    /**
     * Returns whether the current user has the right to change the status of the model
     *
     * @return bool
     */
    public function rightStatus();
}
