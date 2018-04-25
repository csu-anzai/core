<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\Admin;

use Kajona\System\Admin\Formentries\FormentryBase;

/**
 * Interface which indicates that a form entry contains other form entries. Through this it is possible to show
 * validation errors also for nested entries
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 * @module module_system
 */
interface AdminFormgeneratorContainerInterface
{
    /**
     * @return FormentryBase[]
     */
    public function getFields(): array;

    /**
     * @param string $name
     * @return FormentryBase|null
     */
    public function getField($name);//: FormentryBase;

    /**
     * @param string $name
     * @return bool
     */
    public function hasField($name): bool;

    /**
     * @param string $name
     * @return void
     */
    public function removeField($name);
}
