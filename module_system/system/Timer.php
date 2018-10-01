<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Helper to measure timespans, e.g. execution durations
 *
 * @author sidler@mulchprod.de
 */
class Timer
{

    private $start;
    private $end;


    public function start()
    {
        $this->start = microtime(true);
    }

    public function end()
    {
        $this->end = microtime(true);
    }

    public function getDurationsInSec()
    {
        return round(($this->end - $this->start), 5);
    }
}

