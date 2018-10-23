<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Tabbedcontent;

use Kajona\System\View\Components\AbstractComponent;

/**
 * General Tabbedcontent component.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 * @componentTemplate core/module_system/view/components/tabbedcontent/template.twig
 */
class Tabbedcontent extends AbstractComponent
{
    /**
     * @var array
     */
    protected $arrTabs;

    /**
     * @var bool
     */
    protected $bitFullHeight;

    /**
     * Tabbedcontent constructor.
     * @param array $arrTabs
     * @param bool $bitFullHeight
     */
    public function __construct(array $arrTabs, bool $bitFullHeight = false)
    {
        parent::__construct();

        $this->arrTabs = $arrTabs;
        $this->bitFullHeight = $bitFullHeight;
    }

    /**
     * @param array $arrTabs
     */
    public function setArrTabs(array $arrTabs)
    {
        $this->arrTabs = $arrTabs;
    }

    /**
     * @param bool $bitFullHeight
     */
    public function setBitFullHeight(bool $bitFullHeight): void
    {
        $this->bitFullHeight = $bitFullHeight;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $arrTabs = [];
        $arrContents = [];
        $strMainTabId = generateSystemid();
        $bitRemoteContent = false;
        $strClassaddon = "active in ";
        foreach ($this->arrTabs as $strTitle => $strContent) {
            $arrTab['id'] = $arrContent['id'] = generateSystemid();
            $arrTab['title'] = $strTitle;
            $arrTab['classaddon'] = $strClassaddon;
            if (substr($strContent, 0, 7) == 'http://' || substr($strContent, 0, 8) == 'https://') {
                $arrTab['href'] = $strContent;
                $arrContent['classaddon'] = $strClassaddon . "contentLoading";
                $arrContent['content'] = "";
                $bitRemoteContent = true;
            } else {
                $arrTab['href'] = "";
                $arrContent['classaddon'] = $strClassaddon;
                $arrContent['content'] = $strContent;
            }
            $arrTabs[] = $arrTab;
            $arrContents[] = $arrContent;
            $strClassaddon = "";
        }

        $data = [
            "tabs" => $arrTabs,
            "contents" => $arrContents,
            "fullHeight" => $this->bitFullHeight,
            "remoteContent" => $bitRemoteContent,
            "mainTabId" => $strMainTabId
        ];

        return $this->renderTemplate($data);
    }

}
