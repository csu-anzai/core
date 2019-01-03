<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Objectlist;

use Kajona\System\Admin\Formentries\FormentryCheckboxarray;
use Kajona\System\View\Components\Formentry\FormentryComponentAbstract;

/**
 * General objectlist form entry which can display a list of models. It is possible to configure an add button where
 * the user can add new objects or you can also configure a search input which lets the user search objects through an
 * auto complete
 *
 * @author stefan.idler@artemeon.de
 * @since 7.
 * @componentTemplate core/module_system/view/components/formentry/checkboxarray/template.twig
 */
class Checkboxarray extends FormentryComponentAbstract
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var array
     */
    protected $selected;

    /** @var int */
    protected $type = FormentryCheckboxarray::TYPE_CHECKBOX;

    /** @var bool */
    protected $inline = false;


    /**
     * @param string $name
     * @param string $title
     * @param array $items
     * @param array $selected
     */
    public function __construct($name, $title, array $items, array $selected)
    {
        parent::__construct($name, $title);

        $this->items = $items;
        $this->selected = $selected;
    }


    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        $context = parent::buildContext();

        $context['type'] = $this->type;

        $rows = [];
        foreach ($this->items as $key => $value) {
            $rows[] = [
                'key' => $key,
                'title' => $value,
                'checked' => in_array($key, $this->selected) ? 'checked' : '',
                'inline' => $this->inline ? '-inline' : '',
                'readonly' => $this->readOnly ? 'disabled' : '',
                'type' => $this->type,
                'value' => $this->type == FormentryCheckboxarray::TYPE_CHECKBOX ? 'checked' : $key,
                'name' => $this->type == FormentryCheckboxarray::TYPE_CHECKBOX ? $this->name.'['.$key.']' : $this->name,
            ];
        }
        $context["rows"] = $rows;
        return $context;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Checkboxarray
     */
    public function setType(int $type): Checkboxarray
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInline(): bool
    {
        return $this->inline;
    }

    /**
     * @param bool $inline
     * @return Checkboxarray
     */
    public function setInline(bool $inline): Checkboxarray
    {
        $this->inline = $inline;
        return $this;
    }


}
