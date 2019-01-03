<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                          *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System;

/**
 * In case an object is placed in hierarchies, the list entry may render a small path ahead of the entry
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 7.1
 */
interface HierarchicalListableInterface extends AdminListableInterface
{

    /**
     * The path, rendered as a simple text-row
     *
     * @return string
     */
    public function getHierarchicalPath(): string;
}
