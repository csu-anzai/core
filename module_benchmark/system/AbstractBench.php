<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System;



use Kajona\System\System\GenericPluginInterface;

abstract class AbstractBench implements BenchInterface, GenericPluginInterface
{
    const PLUGIN_NAME = "KAJONA.BENCHMARK.BENCH";

    public static function getExtensionName()
    {
        return self::PLUGIN_NAME;
    }

}