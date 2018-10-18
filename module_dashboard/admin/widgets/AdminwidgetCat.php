<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Dashboard\Admin\Widgets;

use Kajona\System\System\Exception;
use Kajona\System\System\Remoteloader;
use Kajona\System\System\StringUtil;

/**
 * @package module_dashboard
 */
class AdminwidgetCat extends Adminwidget implements AdminwidgetInterface
{

    private $arrCats = [
        "<img src='"._webpath_."/files/images/AdminwidgetCat/acrobat.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/banjo.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/burp.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/facepalm.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/knead.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/meal.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/popcorn.gif' style='float: right;'/>",
        "<img src='"._webpath_."/files/images/AdminwidgetCat/sleepy.gif' style='float: right;'/>",
    ];

    /**
     * Basic constructor, registers the fields to be persisted and loaded
     */
    public function __construct()
    {
        parent::__construct();
        //register the fields to be persisted and loaded
        $this->setPersistenceKeys(array("cat"));
    }

    /**
     * Allows the widget to add additional fields to the edit-/create form.
     * Use the toolkit class as usual.
     *
     * @return string
     */
    public function getEditForm()
    {
        $strReturn = $this->getLang("cat_select");

        $strReturn .= $this->objToolkit->formInputRadiogroup("cat", $this->arrCats, $this->getLang("cats"), $this->getFieldValue("cat"));

        return $strReturn;
    }

    /**
     * This method is called, when the widget should generate it's content.
     * Return the complete content using the methods provided by the base class.
     * Do NOT use the toolkit right here!
     *
     * @return string
     */
    public function getWidgetOutput()
    {
        if ($this->getFieldValue("cat") == "") {
            return $this->getLang("cat_setup");
        }
        $strReturn = '<div id="cat-cage" style="height: 150px; width: 100%; background-color: white">';
        $strReturn .= $this->widgetText($this->arrCats[$this->getFieldValue("cat")]);
        $strReturn .= '</div><div style="clear: both;"></div>';

        return $strReturn;
    }

    /**
     * Return a short (!) name of the widget.
     *
     * @return string
     */
    public function getWidgetName()
    {
        return $this->getLang("cat_name");
    }

}
