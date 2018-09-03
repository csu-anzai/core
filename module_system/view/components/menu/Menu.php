<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Menu;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Simple menu component which renders a dropdown menu
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/menu/template.twig
 */
class Menu extends AbstractComponent
{
    /**
     * @var array
     */
    private $items;

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param MenuItem $item
     */
    public function addItem(MenuItem $item)
    {
        $id = $item->getId();
        if (!empty($id)) {
            $this->items[$id] = $item;
        } else {
            $this->items[] = $item;
        }
    }

    /**
     * @param array $items
     */
    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return count($this->items) > 0;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "items" => $this->items,
        ];

        return $this->renderTemplate($data);
    }
}
