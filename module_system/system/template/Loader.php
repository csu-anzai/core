<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Template;

use Kajona\System\System\Resourceloader;

/**
 * Custom twig file loader which also tries to load a template inside a .phar archive
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Loader extends \Twig_Loader_Filesystem
{
    protected function findTemplate($name, $throw = true)
    {
        $parts = explode("/", $name);
        $core = array_shift($parts);
        $module = array_shift($parts);

        $file = Resourceloader::getInstance()->getAbsolutePathForModule($module) . "/" . implode("/", $parts);

        if (is_file($file)) {
            return $this->cache[$name] = $file;
        }

        if ($throw) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name, $file));
        } else {
            return false;
        }
    }
}
