<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Menu\Item;

use Kajona\System\View\Components\Menu\MenuItem;

/**
 * Dialog
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Dialog extends MenuItem
{
    public function __construct($title, $link)
    {
        $title = htmlspecialchars($title);
        $link = htmlspecialchars($link);

        $this->setFullEntry("<li class=\"core-component-menu-item\"><a href=\"#\" onclick='require(\"dialogHelper\").showIframeDialog(\"{$link}\", \"{$title}\"); return false;'>{$title}</a></li>");
    }
}
