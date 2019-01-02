<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\Filesystem;

class Database4JoinQueries extends AbstractBench
{

    public function bench()
    {
        $this->createComplexJoin();
        $this->orderByQuery();
        $this->groupByQuery();
    }


    private function createComplexJoin()
    {
        Database::getInstance()->getPArray(
            "
                SELECT b1a.bench_char20 
                  FROM agp_bench_1 as b1a
            INNER JOIN agp_bench_1 as b1b ON b1a.bench_int = b1b.bench_int
             LEFT JOIN agp_bench_2 as b2a ON b1b.bench_int = b2a.bench_int
            ",
            []
        );
    }

    private function orderByQuery()
    {
        Database::getInstance()->getPArray(
            "
                SELECT b2a.*
                  FROM agp_bench_1 as b1a
            INNER JOIN agp_bench_2 as b2a ON b1a.bench_int = b2a.bench_int
              ORDER BY b1a.bench_double DESC, b2a.bench_int ASC 
            ",
            []
        );
    }

    private function groupByQuery()
    {
        Database::getInstance()->getPArray(
            "
                SELECT b1b.bench_char20, b1a.bench_char20, count(*) 
                  FROM agp_bench_1 as b1a
            INNER JOIN agp_bench_1 as b1b ON b1a.bench_int = b1b.bench_int
              GROUP BY b1b.bench_char20, b1a.bench_char20
            ",
            []
        );
    }
}