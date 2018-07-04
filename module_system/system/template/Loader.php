<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Template;

/**
 * Custom twig file loader which also tries to load a template inside a .phar archive. This allows us to use a simple
 * template path like: core/module_system/view/components/listsearch/template.twig
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Loader extends \Twig_Loader_Filesystem
{
    protected function findTemplate($name, $throw = true)
    {
        $filePath = parent::findTemplate($name, false);

        if ($filePath === false) {
            // in this case we try to load the file from an phar archive
            $parts = explode("/", $name);

            if (isset($parts[1]) && substr($parts[1], 0, 7) == "module_") {
                $parts[1] = $parts[1] . ".phar";
                $name = implode("/", $parts);

                $filePath = parent::findTemplate($name, true);
            }
        }

        return $filePath;
    }
}
