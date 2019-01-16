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
use Kajona\Benchmark\System\Bench\Database3aListLobQueries;
use Kajona\Benchmark\System\Bench\Database3ListQueries;
use Kajona\Benchmark\System\BenchInterface;
use Kajona\System\System\Config;
use Kajona\System\System\Pluginmanager;
use Kajona\System\System\Timer;

class Bench
{

    const ITERATIONS = 2;

    public function main()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $manager = new Pluginmanager(AbstractBench::PLUGIN_NAME, "/system/bench");
        $timer = new Timer();
        $results = [];

        echo "<pre>";
        echo "Kajona system benchmark suite".PHP_EOL;
        echo "(c) ARTEMEON Management Partner".PHP_EOL;
        echo PHP_EOL;
        echo "Database driver: ".Config::getInstance()->getConfig("dbdriver").PHP_EOL;
        echo "Database host:   ".Config::getInstance()->getConfig("dbhost").PHP_EOL;


        echo PHP_EOL;
        echo "| ".str_pad("Bench", 60)."| ".str_pad("Duration", 30)."|".PHP_EOL;
        echo "|-".str_pad("", 60, "-")."|-".str_pad("", 30, "-")."|".PHP_EOL;
        ob_flush();
        flush();

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            /** @var BenchInterface $bench */
            foreach ($manager->getPlugins() as $bench) {

//                if (!$bench instanceof Database3aListLobQueries) {
//                    continue;
//                }

                echo "| ".str_pad(get_class($bench), 60)."| ";
                ob_flush();
                flush();

                $timer->start();
                $bench->bench();
                $timer->end();

                echo str_pad($timer->getDurationsInSec()." sec", 30)."|".PHP_EOL;
                ob_flush();
                flush();
            }

            echo "|-".str_pad("", 60, "-")."|-".str_pad("", 30, "-")."|".PHP_EOL;
        }

        echo PHP_EOL;
        echo "Finished.".PHP_EOL;
        echo "</pre>";

    }

}

(new Bench())->main();