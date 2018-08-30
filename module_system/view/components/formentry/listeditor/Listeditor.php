<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Listeditor;

use Kajona\System\View\Components\Formentry\FormentryComponentAbstract;

/**
 * The listeditor is a form entry which can be used to edit long text options. It is lists all text items and preserves
 * the array key for each option. In case a new option is added the component generate a new random id
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 * @componentTemplate template.twig
 */
class Listeditor extends FormentryComponentAbstract
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @param string $name
     * @param string $title
     * @param array $items
     */
    public function __construct($name, $title, array $items)
    {
        parent::__construct($name, $title);

        $this->items = $items;
    }

    /**
     * @param string $item
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        $context = parent::buildContext();
        $context["items"] = $this->items;

        return $context;
    }

}
