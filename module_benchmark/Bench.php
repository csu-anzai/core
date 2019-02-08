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
        echo "(c) ARTEMEON Management Partner".PHP_EOL;
        echo "AGP environment check and system benchmark suite".PHP_EOL;
        echo PHP_EOL;
        echo "Database driver: ".Config::getInstance()->getConfig("dbdriver").PHP_EOL;
        echo "Database host:   ".Config::getInstance()->getConfig("dbhost").PHP_EOL;
        echo "Database user:   ".Config::getInstance()->getConfig("dbusername").PHP_EOL;
        echo "Database name:   ".Config::getInstance()->getConfig("dbname").PHP_EOL;
        echo "Webserver host:  ".$_SERVER['SERVER_NAME']." / ".$_SERVER['SERVER_ADDR']    .PHP_EOL;
        echo "Webserver:       ".$_SERVER['SERVER_SOFTWARE']    .PHP_EOL;
        echo "Operation System:".PHP_OS    .PHP_EOL;
        echo "OS details:      ". php_uname() .PHP_EOL;
        echo "Request:         ".date("d.m.Y H:i:s")." from ".$_SERVER['REMOTE_ADDR']    .PHP_EOL;


        echo PHP_EOL;
        echo "| ".str_pad("Bench", 60)."| ".str_pad("Duration", 30)."|".PHP_EOL;
        echo "|-".str_pad("", 60, "-")."|-".str_pad("", 30, "-")."|".PHP_EOL;
        @ob_flush();
        flush();

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            /** @var BenchInterface $bench */
            foreach ($manager->getPlugins() as $bench) {
                echo "| ".str_pad(get_class($bench), 60)."| ";
                @ob_flush();
                flush();

                $timer->start();
                $bench->bench();
                $timer->end();

                echo str_pad($timer->getDurationsInSec()." sec", 30)."|".PHP_EOL;
                @ob_flush();
                flush();
            }

            echo "|-".str_pad("", 60, "-")."|-".str_pad("", 30, "-")."|".PHP_EOL;
        }

        echo PHP_EOL;
        $this->checkModules();

        echo PHP_EOL;
        echo "Finished.".PHP_EOL;
        echo "</pre>";

    }
    private $AGPPHPEXTENSIONS = array(
        "curl",
        "exif",
        "fileinfo",
        "gd",
        "iconv",
        "json",
        "ldap",
        "libxml",
        "mbstring",
        "openssl",
        "Zend OPcache",
        "pcre",
        "Phar",
        "Reflection",
        "session",
        "SimpleXML",
        "sockets",
        "SPL",
        "xml",
        "xmlreader",
        "xmlwriter",
        "xsl",
        "zip",
        "SourceGuardian"
    );

    private function checkModules() {
        echo PHP_EOL;
        echo "-".str_pad("", 120, "-").PHP_EOL;
        echo "Check required php extensions".PHP_EOL;
        foreach($this->AGPPHPEXTENSIONS AS $one) {
            echo str_pad($one,25);
            if (in_array($one, get_loaded_extensions())) {
                echo " Loaded!".PHP_EOL;
            }
            else echo " Missing...".PHP_EOL;
        }
        echo PHP_EOL;

        //### show all loaded extensions ###
        //echo "Loaded php extensions".PHP_EOL;
        //print_r(get_loaded_extensions());

        echo "-".str_pad("", 120, "-").PHP_EOL;
        echo PHP_EOL;
        echo "Check PHP values".PHP_EOL;

        /*
        max_execution_time   = 3600
        memory_limit =1024M
        post_max_size = 20M
        upload_max_filesize = 20M
        date.timezone = Europe/Berlin
        phar.readonly = On
        mail.add_x_header = On
        allow_url_fopen = On
        opcache.enable=1
        */

        echo str_pad("current value",15). " | should be ".PHP_EOL;
        echo "-".str_pad("", 40, "-").PHP_EOL;
        echo str_pad(ini_get('max_execution_time'),15). " | 3600".PHP_EOL;
        echo str_pad(ini_get('memory_limit'),15). " | 1024M".PHP_EOL;
        echo str_pad(ini_get('post_max_size'),15). " | 20M".PHP_EOL;
        echo str_pad(ini_get('upload_max_filesize'),15). " | 20M".PHP_EOL;
        echo str_pad(ini_get('date.timezone'),15). " | Europe/Berlin".PHP_EOL;
        echo str_pad(ini_get('phar.readonly'),15). " | On or 1".PHP_EOL;
        echo str_pad(ini_get('mail.add_x_header'),15). " | On or 1".PHP_EOL;
        echo str_pad(ini_get('allow_url_fopen'),15). " | On or 1".PHP_EOL;
        echo str_pad(ini_get('opcache.enable'),15). " | 1".PHP_EOL;
        echo PHP_EOL;

        echo "-".str_pad("", 120, "-").PHP_EOL;
        echo PHP_EOL;
        echo "Checking sys_temp_dir...".PHP_EOL;
        echo str_pad(ini_get('sys_temp_dir'),15). " | should be writeable!".PHP_EOL;

        echo "-".str_pad("", 120, "-").PHP_EOL;
        echo PHP_EOL;
        echo "Check PHP environment".PHP_EOL;
        echo str_pad("extension_dir: ".ini_get('extension_dir'),15) .PHP_EOL;

        if(PHP_OS=="WINNT") {
            echo PHP_EOL;
            echo PHP_EOL."We are running under Windows! Checking PATH...".PHP_EOL;
            echo exec("path");
            echo PHP_EOL."=> Please verify!! The PHP dir MUSST BE in PATH!!!".PHP_EOL;
        }
        echo "-".str_pad("", 120, "-").PHP_EOL;


    }
}

(new Bench())->main();