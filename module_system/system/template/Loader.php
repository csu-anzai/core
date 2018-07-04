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

        if (substr($name, 0, 7) == "phar://") {
            $name = substr($name, 7);
        }

        return parent::findTemplate($name, $throw);
    }
}
