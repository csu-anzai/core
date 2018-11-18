<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Dashboard\View\Components\WidgetList;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Widget list component.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 * @componentTemplate core_agp/module_dashboard/view/components/widgetlist/template.twig
 */
class WidgetList extends AbstractComponent
{
    /**
     * @var array
     */
    protected $widgets;

    /**
     * WidgetList constructor.
     * @param array|null $widgets
     * @param string|null $description
     */
    public function __construct(array $widgets = null)
    {
        parent::__construct();

        $this->widgets = $widgets;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "widgets" => $this->widgets,
        ];

        return $this->renderTemplate($data);
    }
}
