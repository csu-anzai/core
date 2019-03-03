<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\Filesystem;
use Kajona\System\System\StringUtil;

class Database1CreateBench extends AbstractBench
{
    public function bench()
    {

        foreach (Database::getInstance()->getTables() as $name) {
            if (StringUtil::indexOf($name, "agp_bench") !== false) {
                Database::getInstance()->_pQuery("DROP TABLE ".$name, []);
            }
        }

        $this->createTables();
    }


    private function createTables()
    {
        Database::getInstance()->createTable(
            "agp_bench_1",
            [
                "bench_id"          => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char20"      => [DbDatatypes::STR_TYPE_CHAR20, true],
                "bench_char100"     => [DbDatatypes::STR_TYPE_CHAR100, true],
                "bench_char254"     => [DbDatatypes::STR_TYPE_CHAR254, true],
                "bench_char500"     => [DbDatatypes::STR_TYPE_CHAR500, true],
                "bench_charText"    => [DbDatatypes::STR_TYPE_TEXT, true],
                "bench_int"         => [DbDatatypes::STR_TYPE_INT, true],
                "bench_long"        => [DbDatatypes::STR_TYPE_LONG, true],
                "bench_double"      => [DbDatatypes::STR_TYPE_DOUBLE, true],
            ],
            ["bench_id"]
        );


        Database::getInstance()->createTable(
            "agp_bench_2",
            [
                "bench_id" => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char20" => [DbDatatypes::STR_TYPE_CHAR20, true],
                "bench_char100" => [DbDatatypes::STR_TYPE_CHAR100, true],
                "bench_char254" => [DbDatatypes::STR_TYPE_CHAR254, true],
                "bench_char500" => [DbDatatypes::STR_TYPE_CHAR500, true],
                "bench_charText" => [DbDatatypes::STR_TYPE_TEXT, true],
                "bench_int" => [DbDatatypes::STR_TYPE_INT, true],
                "bench_long" => [DbDatatypes::STR_TYPE_LONG, true],
                "bench_double" => [DbDatatypes::STR_TYPE_DOUBLE, true],
            ],
            ["bench_id"],
            ["bench_char20", "bench_int"]
        );


        Database::getInstance()->createTable(
            "agp_bench_1_lob",
            [
                "bench_id"          => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char20"      => [DbDatatypes::STR_TYPE_CHAR20, true],
                "bench_char100"     => [DbDatatypes::STR_TYPE_CHAR100, true],
                "bench_char254"     => [DbDatatypes::STR_TYPE_CHAR254, true],
                "bench_char500"     => [DbDatatypes::STR_TYPE_CHAR500, true],
                "bench_charLongtext" => [DbDatatypes::STR_TYPE_LONGTEXT, true],
                "bench_int"         => [DbDatatypes::STR_TYPE_INT, true],
                "bench_long"        => [DbDatatypes::STR_TYPE_LONG, true],
                "bench_double"      => [DbDatatypes::STR_TYPE_DOUBLE, true],
            ],
            ["bench_id"]
        );


        Database::getInstance()->createTable(
            "agp_bench_2_lob",
            [
                "bench_id" => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char20" => [DbDatatypes::STR_TYPE_CHAR20, true],
                "bench_char100" => [DbDatatypes::STR_TYPE_CHAR100, true],
                "bench_char254" => [DbDatatypes::STR_TYPE_CHAR254, true],
                "bench_char500" => [DbDatatypes::STR_TYPE_CHAR500, true],
                "bench_charLongtext" => [DbDatatypes::STR_TYPE_LONGTEXT, true],
                "bench_int" => [DbDatatypes::STR_TYPE_INT, true],
                "bench_long" => [DbDatatypes::STR_TYPE_LONG, true],
                "bench_double" => [DbDatatypes::STR_TYPE_DOUBLE, true],
            ],
            ["bench_id"],
            ["bench_char20", "bench_int"]
        );
    }

}