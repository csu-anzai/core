<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\Filesystem;

class Database0ConnectBench extends AbstractBench
{
    public function bench()
    {
        Database::getInstance()->getTables();
    }

}