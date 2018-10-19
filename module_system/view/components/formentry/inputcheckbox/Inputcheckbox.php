<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Inputcheckbox;

use Kajona\System\View\Components\Formentry\FormentryComponentAbstract;

/**
 * Inputcheckbox
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/formentry/inputcheckbox/template.twig
 */
class Inputcheckbox extends FormentryComponentAbstract
{
    /**
     * @var bool
     */
    protected $checked;

    /**
     * @param string $name
     * @param string $title
     * @param bool $checked
     */
    public function __construct(string $name, string $title, bool $checked)
    {
        parent::__construct($name, $title);

        $this->checked = $checked;
    }

    /**
     * @param bool $checked
     */
    public function setChecked(bool $checked)
    {
        $this->checked = $checked;
    }

    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        $context = parent::buildContext();
        $context["checked"] = $this->checked;

        return $context;
    }
}
