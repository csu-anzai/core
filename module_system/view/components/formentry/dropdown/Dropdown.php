<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Dropdown;

use Kajona\System\System\Carrier;
use Kajona\System\View\Components\Formentry\FormentryComponentAbstract;

/**
 * Dropdown
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/formentry/dropdown/template.twig
 */
class Dropdown extends FormentryComponentAbstract
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $selected;

    /**
     * @var string
     */
    protected $addons;

    /**
     * @param string $name
     * @param string $title
     * @param array $options
     * @param string $selected
     */
    public function __construct(string $name, string $title, array $options, $selected = null)
    {
        parent::__construct($name, $title);

        $this->options = $options;
        $this->selected = $selected;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $selected
     */
    public function setSelected(string $selected)
    {
        $this->selected = $selected;
    }

    /**
     * @param string $addons
     * @deprecated
     */
    public function setAddons($addons)
    {
        $this->addons = $addons;
    }

    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        // set default data attributes
        if (!isset($this->data["placeholder"])) {
            $this->data["placeholder"] = Carrier::getInstance()->getObjLang()->getLang("commons_dropdown_dataplaceholder", "system");
        }

        $context = parent::buildContext();
        $context["options"] = $this->options;
        $context["selected"] = $this->selected;
        $context["no_select"] = $this->selected === null || $this->selected === '';
        $context["addons"] = $this->addons;

        return $context;
    }
}
