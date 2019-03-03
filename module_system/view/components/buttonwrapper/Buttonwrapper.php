<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Buttonwrapper;

use Kajona\System\View\Components\AbstractComponent;

/**
 * A button wrapper
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/buttonwrapper/template.twig
 */
class Buttonwrapper extends AbstractComponent
{
    /**
     * @var mixed
     */
    protected $buttons;

    /**
     * @param string $name
     * @param string $title
     * @param mixed $value
     */
    public function __construct($buttons)
    {
        $this->buttons = $buttons;
        parent::__construct();
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderComponent(): string
    {
        return $this->renderTemplate(["content" => $this->buttons]);
    }
}
