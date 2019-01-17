<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                            *
********************************************************************************************************/

namespace Kajona\Dashboard\Admin\Widgets;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryDropdown;
use Kajona\System\Admin\Formentries\FormentryText;
use Kajona\System\System\Exception;
use Kajona\System\System\Remoteloader;
use Kajona\System\System\StringUtil;

/**
 * @package module_dashboard
 */
class AdminwidgetWeather extends Adminwidget implements AdminwidgetInterface
{

    /**
     * @var string
     */
    private $imgFileName = "weather.png";

    /**
     * Basic constructor, registers the fields to be persisted and loaded
     */
    public function __construct()
    {
        parent::__construct();
        //register the fields to be persisted and loaded
        $this->setPersistenceKeys(array("unit", "location"));
    }


    /**
     * @param AdminFormgenerator $form
     */
    public function getEditFormContent(AdminFormgenerator $form)
    {
        $form->addField(new FormentryText("location", ""), "")
            ->setStrValue($this->getFieldValue("location"))
            ->setStrLabel($this->getLang("weather_location"));
        $form->addField(new FormentryDropdown("unit", ""))
            ->setArrKeyValues(["f" => $this->getLang("weather_fahrenheit"), "c" => $this->getLang("weather_celsius")])
            ->setStrValue(($this->getFieldValue("unit")))
            ->setStrLabel($this->getLang("weather_unit"));
    }

    /**
     * This method is called, when the widget should generate it's content.
     * Return the complete content using the methods provided by the base class.
     * Do NOT use the toolkit right here!
     *
     * @return string
     * @throws Exception
     */
    public function getWidgetOutput()
    {
        $strReturn = "";

        if ($this->getFieldValue("location") == "") {
            return $this->getEditWidgetForm();
        }

        if (StringUtil::indexOf($this->getFieldValue("location"), "GM") !== false) {
            $strReturn = "This widget changed, please update your location by editing the widget";
            $strReturn .= $this->getEditWidgetForm();
            return $strReturn;
        }


        //request the xml...
        try {
            $strFormat = "metric";
            if ($this->getFieldValue("unit") == "f") {
                $strFormat = "imperial";
            }

            $objRemoteloader = new Remoteloader();
            $objRemoteloader->setStrHost("api.openweathermap.org");
            $objRemoteloader->setStrQueryParams("/data/2.5/forecast/daily?APPID=4bdceecc2927e65c5fb712d1222c5293&q=".$this->getFieldValue("location")."&units=".$strFormat."&cnt=4");
            $strContent = $objRemoteloader->getRemoteContent();
        } catch (Exception $objExeption) {
            $strContent = "";
        }

        if ($strContent != "" && json_decode($strContent, true) !== null) {
            $arrResponse = json_decode($strContent, true);
            $strReturn .= $this->widgetText($this->getLang("weather_location_string").$arrResponse["city"]["name"].", ".$arrResponse["city"]["country"]);


            foreach ($arrResponse["list"] as $arrOneForecast) {
                $objDate = new \Kajona\System\System\Date($arrOneForecast["dt"]);
                $strReturn .= "<div>";
                $strReturn .= $this->widgetText("<div style='float: left;'>".dateToString($objDate, false).": ".round($arrOneForecast["temp"]["day"], 1)."°</div>");
                $strReturn .= $this->widgetText("<img src='http://openweathermap.org/img/w/".$arrOneForecast["weather"][0]["icon"].".png' style='float: right;' />");
                $strReturn .= "</div><div style='clear: both;'></div>";
            }

        } else {
            $strReturn .= $this->getLang("weather_errorloading");
        }


        return $strReturn;
    }

    /**
     * Return a short (!) name of the widget.
     *
     * @return string
     */
    public function getWidgetName()
    {
        return $this->getLang("weather_name");
    }

    /**
     * @inheritdoc
     */
    public function getWidgetDescription()
    {
        return $this->getLang("weather_description");
    }

    /**
     * @return string
     */
    public function getImgFileName(): string
    {
        return $this->imgFileName;
    }
}
