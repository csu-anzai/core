<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\Filesystem;

class Database3ListQueries extends AbstractBench
{

    public function bench()
    {
        $this->generatorQuery();
        $this->fullQuery();
        $this->countQuery();
    }


    private function generatorQuery()
    {
        foreach(Database::getInstance()->getGenerator("SELECT * FROM agp_bench_1 ORDER BY bench_id ASC", []) as $rows) {

        }
    }

    private function fullQuery()
    {
        Database::getInstance()->getPArray("SELECT * FROM agp_bench_2 ORDER BY bench_int DESC", []);
    }

    private function countQuery()
    {
        Database::getInstance()->getPRow("SELECT count(*) as anz FROM agp_bench_1", [])["anz"];
        Database::getInstance()->getPRow("SELECT count(*) as anz FROM agp_bench_2 WHERE bench_int > ? AND bench_double < ?", [2000, 1.2])["anz"];
    }
}