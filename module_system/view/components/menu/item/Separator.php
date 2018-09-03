<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Menu\Item;

use Kajona\System\View\Components\Menu\MenuItem;

/**
 * Separator
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Separator extends MenuItem
{
    public function __construct()
    {
        $this->setFullEntry("<li role=\"separator\" class=\"divider\"></li>");
    }
}
