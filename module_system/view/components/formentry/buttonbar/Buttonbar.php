<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Buttonbar;

use Kajona\System\View\Components\Formentry\Dropdown\Dropdown;

/**
 * Buttonbar
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/formentry/buttonbar/template.twig
 */
class Buttonbar extends Dropdown
{
    const TYPE_CHECKBOX = "checkbox";
    const TYPE_RADIO = "radio";

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $name
     * @param string $title
     * @param array $options
     * @param array $selected
     * @param string $type
     */
    public function __construct(string $name, string $title, array $options, array $selected = null, $type = null)
    {
        parent::__construct($name, $title, $options, $selected);

        $this->type = $type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        $context = parent::buildContext();
        $context["type"] = $this->type === self::TYPE_RADIO ? "radio" : "checkbox";

        return $context;
    }
}
