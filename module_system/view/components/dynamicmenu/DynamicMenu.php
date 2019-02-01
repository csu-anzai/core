<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Dynamicmenu;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Simple menu component loads entries on click using a ajax call
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/dynamicmenu/template.twig
 */
class DynamicMenu extends AbstractComponent
{

    private $button;
    private $endpoint;

    /**
     * DynamicMenu constructor.
     * @param string $button
     * @param string $endpoint
     */
    public function __construct(string $button, string $endpoint)
    {
        parent::__construct();
        $this->button = $button;
        $this->endpoint = $endpoint;
    }


    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "button" => $this->button,
            "endpoint" => $this->endpoint,
            "menuid" => generateSystemid()
        ];

        return $this->renderTemplate($data);
    }
}
