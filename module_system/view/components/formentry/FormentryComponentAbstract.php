<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Formentry;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Base class for all form entry components. It provides the base name and title attributes which each form entry must
 * have, it does not contain a value property. You should overwrite the `buildContext` method in each form entry to
 * extend the context for your form entry
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
abstract class FormentryComponentAbstract extends AbstractComponent
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $readOnly = false;

    /**
     * @param string $name
     * @param string $title
     */
    public function __construct($name, $title)
    {
        parent::__construct();

        $this->name = $name;
        $this->title = $title;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly(bool $readOnly)
    {
        $this->readOnly = $readOnly;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        return $this->renderTemplate($this->buildContext());
    }

    /**
     * Each form entry should overwrite this method to provide custom values specific to the form entry
     *
     * @return array
     */
    protected function buildContext()
    {
        return [
            "name" => $this->name,
            "title" => $this->title,
            "readOnly" => $this->readOnly,
        ];
    }
}