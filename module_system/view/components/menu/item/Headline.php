<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Menu\Item;

use Kajona\System\View\Components\Menu\MenuItem;

/**
 * Headline
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Headline extends MenuItem
{
    public function __construct($title)
    {
        $this->setFullEntry("<li class=\"dropdown-header\">" . htmlspecialchars($title) . "</li>");
    }
}
