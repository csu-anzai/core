<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Database;
use Kajona\System\System\Date;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\Filesystem;

class Database2FillBench extends AbstractBench
{

    const INSERT_ROWS = 2000;
    private $generatedStrings = [];

    public function bench()
    {
        $this->fillTableSingleInsert();
        $this->fillTableMultiInsert();
    }


    private function fillTableSingleInsert()
    {
        for ($i = 0; $i < self::INSERT_ROWS; $i++) {
            Database::getInstance()->_pQuery(
                "INSERT INTO agp_bench_1 (bench_id, bench_char20, bench_char100, bench_char254, bench_char500, bench_charText, bench_int, bench_long, bench_double) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                $this->getRandomRow()
            );
        }
    }


    private function fillTableMultiInsert()
    {
        $rows = [];
        for ($i = 0; $i < self::INSERT_ROWS; $i++) {
            $rows[] = $this->getRandomRow();
        }

        Database::getInstance()->multiInsert("agp_bench_2", ["bench_id", "bench_char20", "bench_char100", "bench_char254", "bench_char500", "bench_charText", "bench_int", "bench_long", "bench_double"], $rows);
    }

    private function getRandomRow()
    {
        return [
            generateSystemid(),
            $this->generateRandomString(20),
            $this->generateRandomString(100),
            $this->generateRandomString(254),
            $this->generateRandomString(500),
            $this->generateRandomString(2000),
            rand(1, 3200000),
            Date::getCurrentTimestamp(),
            (float)rand(1, 40)/11.2
        ];
    }


    private function generateRandomString($length)
    {
        if (isset($this->generatedStrings[$length])) {
            return $this->generatedStrings[$length];
        }
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $this->generatedStrings[$length] = $randomString;
    }

}