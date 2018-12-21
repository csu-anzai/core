<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Dashboard\View\Components\Widget;

use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\Carrier;
use Kajona\System\System\StringUtil;
use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a table filled with infos.
 * The header may be build using cssclass -> value or index -> value arrays
 * Values may be build using cssclass -> value or index -> value arrays, too (per row)
 * For header, the passing of the fake-classes colspan-2 and colspan-3 are allowed in order to combine cells
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 * @componentTemplate core/module_dashboard/view/components/widget/template.twig
 */
class Widget extends AbstractComponent
{

    private $title = "";
    private $subTitle = "";

    private $actions = [];
    private $content = "";
    private $id = "";


    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        //transform the action to a menu
        $entries = [];
        foreach ($this->actions as $strAction) {
            $entries[] = ["fullentry" => $strAction];
        }

        $menu = Carrier::getInstance()->getObjToolkit()->listButton(
            "<span class='dropdown'><a href='#' data-toggle='dropdown' role='button'>".AdminskinHelper::getAdminImage("icon_submenu")."</a>".Carrier::getInstance()->getObjToolkit()->registerMenu($this->id, $entries, true)."</span>"
        );

        return$this->renderTemplate([
            "title" => $this->title,
            "subTitle" => $this->subTitle,
            "menu" => $menu,
            "content" => $this->content,
            "widgetId" => $this->id
        ]);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Widget
     */
    public function setTitle(string $title): Widget
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubTitle(): string
    {
        return $this->subTitle;
    }

    /**
     * @param string $subTitle
     * @return Widget
     */
    public function setSubTitle(string $subTitle): Widget
    {
        $this->subTitle = $subTitle;
        return $this;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     * @return Widget
     */
    public function setActions(array $actions): Widget
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Widget
     */
    public function setContent(string $content): Widget
    {
        $this->content = $content;
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
     * @param string $id
     * @return Widget
     */
    public function setId(string $id): Widget
    {
        $this->id = $id;
        return $this;
    }


}