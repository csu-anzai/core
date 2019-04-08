<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Classloader;
use Kajona\System\System\Date;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\Root;
use Kajona\System\System\SystemChangelogRestorer;
use Kajona\System\System\VersionableInterface;

/**
 * Tries to restore every versionable entity
 *
 * @author christoph.kappestein@artemeon.de
 * @package module_system
 */
class SystemChangelogRestorerTest extends Testbase
{
    /**
     * @dataProvider entityProvider
     */
    public function testRestore($model)
    {
        $restorer = new SystemChangelogRestorer();
        $restorer->restoreObject($model, new Date());

        $this->assertInstanceOf(VersionableInterface::class, $model);
    }

    public function entityProvider()
    {
        $filter = function (&$strOneFile, $strPath) {
            $instance = Classloader::getInstance()->getInstanceFromFilename($strPath, Root::class);
            if ($instance instanceof VersionableInterface) {
                $strOneFile = get_class($instance);
            } else {
                $strOneFile = null;
            }
        };

        $classes = Resourceloader::getInstance()->getFolderContent("/system", array(".php"), false, null, $filter);
        $classes = array_values(array_filter($classes));
        $classes = array_map(function($class){ return [ new $class() ]; }, $classes);

        return $classes;
    }
}

