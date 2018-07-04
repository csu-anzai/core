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
        $name = str_replace(_realpath_, "", str_replace("\\", "/", $name));
        $phar = false;

        if (substr($name, 0, 7) == "phar://") {
            $name = substr($name, 7);
            $phar = true;
        }

        if ($phar) {
            // in case we have a phar use a special logic
            $allFiles = [];
            foreach ($this->paths as $namespace => $paths) {
                foreach ($paths as $path) {
                    $filePath = "phar://" . $path . "/" . $name;
                    if (is_file($filePath)) {
                        return $this->cache[$name] = $filePath;
                    }

                    $allFiles[] = $filePath;
                }
            }

            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into phar: %s).', $name, implode(', ', $allFiles)));
        } else {
            // in case we have no phar use the normal logic
            return parent::findTemplate($name, false);
        }
    }
}
