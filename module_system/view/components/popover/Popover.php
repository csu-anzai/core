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
 * @componentTemplate template.twig
 */
class Popover extends AbstractComponent
{
    private $title = "";
    /**
     * @var string one of click | hover | focus | manual
     */
    private $link = "";
    private $content = "";
    private $trigger = "hover";



    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "title" => $this->title,
            "content" => $this->content,
            "link" => $this->link,
            "trigger" => $this->trigger,
            "id" => generateSystemid()
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


}
