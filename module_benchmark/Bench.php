<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                                   *
********************************************************************************************************/

namespace Kajona\Benchmark;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\Benchmark\System\BenchInterface;
use Kajona\System\System\Pluginmanager;
use Kajona\System\System\Timer;

class Bench
{

    public function main()
    {
        error_reporting(E_ALL);

        $manager = new Pluginmanager(AbstractBench::PLUGIN_NAME, "/system/bench");
        $timer = new Timer();
        $results = [];

        echo "<pre>";
        echo "Kajona system benchmark suite".PHP_EOL;
        echo "(c) ARTEMEON Management Partner".PHP_EOL;


        /** @var BenchInterface $bench */
        foreach ($manager->getPlugins() as $bench) {
            $timer->start();
            $bench->bench();
            $timer->end();
            $results[get_class($bench)] = $timer->getDurationsInSec()." sec";
        }

        echo PHP_EOL;
        echo "| ".str_pad("Bench", 60)."| ".str_pad("Duration", 30)."|".PHP_EOL;
        echo "|".str_pad("", 93, "-")."|".PHP_EOL;
        foreach ($results as $bench => $duration) {
            echo "| ".str_pad($bench, 60)."| ".str_pad($duration, 30)."|".PHP_EOL;
        }


        echo "</pre>";

    }

}

(new Bench())->main();