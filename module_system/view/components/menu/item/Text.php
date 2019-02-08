<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Menu\Item;

use Kajona\System\System\AdminskinHelper;
use Kajona\System\View\Components\Menu\MenuItem;

/**
 * Dialog
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Text extends MenuItem
{
    public function __construct($text)
    {
//        $text = htmlspecialchars($text, ENT_QUOTES);
        $this->setFullEntry("<li class=\"core-component-menu-item\">{$text}</li>");
    }
}
