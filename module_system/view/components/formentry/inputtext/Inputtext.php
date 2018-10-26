<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\View\Components\Formentry\Inputtext;

use Kajona\System\View\Components\Formentry\FormentryComponentAbstract;

/**
 * Inputtext
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/formentry/inputtext/template.twig
 */
class Inputtext extends FormentryComponentAbstract
{
    /**
     * @var array
     */
    private static $allowedTypes = ["color", "date", "month", "number", "password", "range", "search", "tel", "text", "url", "week"];

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $opener;

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
     * @param string $type
     */
    public function setType(string $type)
    {
        if (!in_array($type, self::$allowedTypes)) {
            throw new \InvalidArgumentException("Input type must be one of: " . implode(", ", self::$allowedTypes));
        }

        $this->type = $type;
    }

    /**
     * @param string $opener
     */
    public function setOpener($opener)
    {
        $this->opener = $opener;
    }

    /**
     * @inheritdoc
     */
    public function buildContext()
    {
        $context = parent::buildContext();
        $context["value"] = $this->value;
        $context["type"] = $this->type ?: 'text';
        $context["opener"] = $this->opener;

        return $context;
    }
}
