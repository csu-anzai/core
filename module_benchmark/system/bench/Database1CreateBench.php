<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\Filesystem;

class Database1CreateBench extends AbstractBench
{
    public function bench()
    {
        $this->createTables();
    }


    private function createTables()
    {
        Database::getInstance()->createTable(
            "agp_bench_1",
            [
                "bench_id"          => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char20"      => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char100"     => [DbDatatypes::STR_TYPE_CHAR100, false],
                "bench_char254"     => [DbDatatypes::STR_TYPE_CHAR254, false],
                "bench_char500"     => [DbDatatypes::STR_TYPE_CHAR500, false],
                "bench_charText"    => [DbDatatypes::STR_TYPE_TEXT, false],
                "bench_charLongtext" => [DbDatatypes::STR_TYPE_LONGTEXT, false],
                "bench_int"         => [DbDatatypes::STR_TYPE_INT, false],
                "bench_long"        => [DbDatatypes::STR_TYPE_LONG, false],
                "bench_double"      => [DbDatatypes::STR_TYPE_DOUBLE, false],
            ],
            ["bench_id"]
        );


        Database::getInstance()->createTable(
            "agp_bench_2",
            [
                "bench_id" => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char20" => [DbDatatypes::STR_TYPE_CHAR20, false],
                "bench_char100" => [DbDatatypes::STR_TYPE_CHAR100, false],
                "bench_char254" => [DbDatatypes::STR_TYPE_CHAR254, false],
                "bench_char500" => [DbDatatypes::STR_TYPE_CHAR500, false],
                "bench_charText" => [DbDatatypes::STR_TYPE_TEXT, false],
                "bench_charLongtext" => [DbDatatypes::STR_TYPE_LONGTEXT, false],
                "bench_int" => [DbDatatypes::STR_TYPE_INT, false],
                "bench_long" => [DbDatatypes::STR_TYPE_LONG, false],
                "bench_double" => [DbDatatypes::STR_TYPE_DOUBLE, false],
            ],
            ["bench_id"],
            ["bench_id", "bench_char20", "bench_int"]
        );
    }

}