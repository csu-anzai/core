<?php

namespace Kajona\Fileindexer\System;

use Kajona\Fileindexer\System\Parser;
use Kajona\System\System\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider
 *
 * @package module_fileindexer
 * @author christoph.kappestein@gmail.com
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @see \Kajona\Fileindexer\System\Indexer
     */
    const STR_INDEXER = "fileindexer_indexer";

    /**
     * @see \Kajona\Fileindexer\System\ParserInterface
     */
    const STR_PARSER = "fileindexer_parser";

    public function register(Container $objContainer)
    {
        $objContainer[self::STR_INDEXER] = function ($c) {
            return new Indexer($c[self::STR_PARSER], $c[\Kajona\System\System\ServiceProvider::STR_LOGGER]);
        };

        $objContainer[self::STR_PARSER] = function ($c) {
            $objConfig = Config::getInstance("module_fileindexer");
            return new Parser\Tika($objConfig->getConfig("java_exec"), $objConfig->getConfig("tika_jar"));
        };
    }
}
