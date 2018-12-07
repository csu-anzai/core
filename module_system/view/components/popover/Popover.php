<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Popover;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Renders a simple popover
 *
 * @author stefan.idler@artemeon.de
 * @since 7.0
 * @componentTemplate core/module_system/view/components/popover/template.twig
 */
class Popover extends AbstractComponent
{
    private $title = "";
    /**
     * @var string one of click | hover | focus | manual
     */
    private $link = "";
    private $content = "";
    private $contentEndpoint = "";
    private $trigger = "hover";
    private $placement = "bottom";

    private $id = '';

    /**
     * Popover constructor.
     */
    public function __construct()
    {
        $this->id = generateSystemid();
        parent::__construct();
    }


    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "title" => $this->title,
            "content" => $this->content,
            "contentEndpoint" => $this->contentEndpoint,
            "link" => $this->link,
            "trigger" => $this->trigger,
            "placement" => $this->placement,
            "id" => $this->id,
        ];

        return $this->renderTemplate($data);
    }

    /**
     * @param string $title
     * @return Popover
     */
    public function setTitle(string $title): Popover
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $link
     * @return Popover
     */
    public function setLink(string $link): Popover
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @param string $content
     * @return Popover
     */
    public function setContent(string $content): Popover
    {
        $this->content = $content;
        return $this;
    }

    /**
     * One of click | hover | focus | manual
     * @param string $trigger
     * @return Popover
     */
    public function setTrigger(string $trigger): Popover
    {
        $this->trigger = $trigger;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnShow()
    {
        return $this->onShow;
    }

    /**
     * @param mixed $onShow
     * @return Popover
     */
    public function setOnShow($onShow)
    {
        $this->onShow = $onShow;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContentEndpoint(): string
    {
        return $this->contentEndpoint;
    }

    /**
     * @param string $contentEndpoint
     * @return Popover
     */
    public function setContentEndpoint(string $contentEndpoint): Popover
    {
        $this->contentEndpoint = $contentEndpoint;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacement() : string
    {
        return $this->placement;
    }

    /**
     * @param string $placement
     */
    public function setPlacement(string $placement)
    {
        $this->placement = $placement;
    }
}
