<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\StringUtil;

class Database8DropBench extends AbstractBench
{
    public function bench()
    {
        $this->deleteTables();
    }

    private function deleteTables()
    {
        foreach (Database::getInstance()->getTables() as $name) {
            if (StringUtil::indexOf($name, "agp_bench") !== false) {
              //  Database::getInstance()->_pQuery("DROP TABLE ".$name, []);
            }
        }
    }

}