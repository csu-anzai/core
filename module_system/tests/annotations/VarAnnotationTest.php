<?php

namespace Kajona\System\Tests\Filter;

use Kajona\System\System\BootstrapCache;
use Kajona\System\System\Classloader;
use Kajona\System\System\Reflection;
use Kajona\System\System\Root;
use Kajona\System\System\StringUtil;
use Kajona\System\Tests\Testbase;

class VarAnnotationTest extends Testbase
{

    private static $allowValues = ["string", "objectList", "float", "int", "bool", "Date", "array", "string[]"];


    /**
     * @param string $class
     * @param string $property
     * @param string $tableName
     *
     * @dataProvider varAnnotationProvider
     * @throws \Kajona\System\System\Exception
     */
    public function testVarAnnotationPresent(string $class, string $property, ?string $tableName)
    {
        $reflection = new Reflection($class);

        $varAnnotation = $reflection->getAnnotationValueForProperty($property, "@var");

        $this->assertNotNull($varAnnotation, "Missing @var for {$class}:{$property}");
        $this->assertTrue(in_array($varAnnotation, self::$allowValues), "Wrong @var value {$varAnnotation} for {$class}:{$property}");

        if ($tableName !== null) {
            // table name must start with agp_
            $this->assertTrue(StringUtil::startsWith($tableName, "agp_"), "Missing agp_ prefix at @tableName for {$class}:{$property}");
        }
    }


    public function varAnnotationProvider()
    {
        $map = [];
        $files = $this->getFiles();
        foreach ($files as $classname => $filename) {

            if (StringUtil::indexOf($filename, "/debug/") !== false) {
                continue;
            }
            if (StringUtil::indexOf($filename, "/config/") !== false) {
                continue;
            }
            if (StringUtil::indexOf($filename, "/tests/") !== false) {
                continue;
            }
            if (StringUtil::indexOf($filename, "/ServiceProvider") !== false) {
                continue;
            }

            //skip files on first level
            $dirs = explode("/", dirname(StringUtil::replace(_realpath_, '', $filename)));
            if (count($dirs) == 2) {
                continue;
            }

            try {

                $ref = new \ReflectionClass($classname);
                if (!$ref->isInstantiable()) {
                    continue;
                }

                $isEntity = $ref->isSubclassOf(Root::class);
                $reflection = new Reflection($classname);

                foreach ($reflection->getPropertiesWithAnnotation("@tableColumn") as $prop => $val) {
                    $map[] = [$classname, $prop, $isEntity ? $val : null];
                }
            } catch (\Throwable $e) {
                echo $filename.PHP_EOL;
                echo $e.PHP_EOL;
                throw new \Exception("Failure in class config");
            }
        }

        return $map;
    }


    private function getFiles()
    {
        //rename the packageconfig if present
        if (is_file(_realpath_."project/packageconfig.php")) {
            rename(_realpath_."project/packageconfig.php", _realpath_."project/packageconfig.php.back");
            Classloader::getInstance()->flushCache();
        }

        $arrMergedFiles = BootstrapCache::getInstance()->getCacheContent(BootstrapCache::CACHE_CLASSES);

        if (is_file(_realpath_."project/packageconfig.php.back")) {
            rename(_realpath_."project/packageconfig.php.back", _realpath_."project/packageconfig.php");
            Classloader::getInstance()->flushCache();
        }

        return $arrMergedFiles;
    }


}

