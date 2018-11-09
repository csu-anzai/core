<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;

class Database7DeleteBench extends AbstractBench
{
    const INSERT_ROWS = 1000;

    public function bench()
    {
        $this->fillTableSingleInsert();
        $this->deleteTableRows();
    }

    private function fillTableSingleInsert()
    {
        for ($i = 0; $i < self::INSERT_ROWS; $i++) {
            Database::getInstance()->_pQuery(
                "INSERT INTO agp_bench_2 (bench_id, bench_char20, bench_char100, bench_char254, bench_char500, bench_charText, bench_charLongtext, bench_int, bench_long, bench_double) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                $this->getRandomRow()
            );
        }
    }

    private function deleteTableRows()
    {
        Database::getInstance()->_pQuery("DELETE FROM agp_bench_2 WHERE bench_char20 = ?", ["foo"]);
    }

    private function getRandomRow()
    {
        return [
            generateSystemid(),
            "foo",
            "",
            "",
            "",
            "",
            "",
            10,
            rand(0, PHP_INT_MAX),
            (float)rand(0, 40)/11.2
        ];
    }
}