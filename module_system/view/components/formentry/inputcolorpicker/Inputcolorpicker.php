<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Inputtext;

use Kajona\System\View\Components\Formentry\FormentryComponentAbstract;

/**
 * Inputcolorpicker
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/formentry/inputcolorpicker/template.twig
 */
class Inputcolorpicker extends FormentryComponentAbstract
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string $name
     * @param string $title
     * @param mixed $value
     */
    public function __construct(string $name, string $title, $value = null)
    {
        parent::__construct($name, $title);

        $this->value = $value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        $context = parent::buildContext();
        $context["value"] = $this->value;

        return $context;
    }
}
