<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Dashboard\View\Components\Dashboard;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a table filled with infos.
 * The header may be build using cssclass -> value or index -> value arrays
 * Values may be build using cssclass -> value or index -> value arrays, too (per row)
 * For header, the passing of the fake-classes colspan-2 and colspan-3 are allowed in order to combine cells
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 * @componentTemplate core/module_dashboard/view/components/dashboard/template.twig
 */
class Dashboard extends AbstractComponent
{

    private $widgets = [];


    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {

        return $this->renderTemplate([
            "columnNames" => [
                ["name" => "column1", "widgets" => $this->widgets["column1"] ?? []],
                ["name" => "column2", "widgets" => $this->widgets["column2"] ?? []],
                ["name" => "column3", "widgets" => $this->widgets["column3"] ?? []],
            ]
        ]);
    }

    /**
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    /**
     * @param array $widgets
     * @return Dashboard
     */
    public function setWidgets(array $widgets): Dashboard
    {
        $this->widgets = $widgets;
        return $this;
    }


}