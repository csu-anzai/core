<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        *
********************************************************************************************************/

namespace Kajona\Dashboard\Admin\Widgets;

use Kajona\Packagemanager\System\PackagemanagerManager;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryCheckbox;
use Kajona\System\System\Carrier;
use Kajona\System\System\SystemModule;

/**
 * @package module_dashboard
 *
 */
class AdminwidgetSysteminfo extends Adminwidget implements AdminwidgetInterface
{

    /**
     * @var string
     */
    private $imgFileName = "systeminfo.png";

    /**
     * Basic constructor, registers the fields to be persisted and loaded
     *
     */
    public function __construct()
    {
        parent::__construct();
        //register the fields to be persisted and loaded
        $this->setPersistenceKeys(array("php", "server", "kajona"));
    }

    /**
     * @inheritdoc
     */
    public function getEditFormContent(AdminFormgenerator $form)
    {

        $form->addField(new FormentryCheckbox("", "php"), "")
            ->setStrLabel($this->getLang("sysinfo_checkboxphp"))
            ->setStrValue($this->getFieldValue("php"));
        $form->addField(new FormentryCheckbox("", "server"), "")
            ->setStrLabel($this->getLang("sysinfo_checkboxserver"))
            ->setStrValue($this->getFieldValue("server"));
        $form->addField(new FormentryCheckbox("", "kajona"), "")
            ->setStrLabel($this->getLang("sysinfo_checkboxkajona"))
            ->setStrValue($this->getFieldValue("kajona"));

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
        $strReturn = "";

        if (!SystemModule::getModuleByName("system")->rightView() || !Carrier::getInstance()->getObjSession()->isSuperAdmin()) {
            return $this->getLang("commons_error_permissions");
        }

        if ($this->getFieldValue("php") == "" && $this->getFieldValue("server") == "" && $this->getFieldValue("kajona") == "") {
            return $this->getEditWidgetForm();
        }

        //check wich infos to produce
        if ($this->getFieldValue("php") == "checked") {
            $strReturn .= $this->widgetText($this->getLang("sysinfo_php_version").PHP_VERSION);
            $strReturn .= $this->widgetText($this->getLang("sysinfo_php_memlimit").bytesToString(ini_get("memory_limit"), true));
            $strReturn .= $this->widgetSeparator();
        }
        if ($this->getFieldValue("server") == "checked") {
            $strReturn .= $this->widgetText($this->getLang("sysinfo_server_system").php_uname("s")." ".php_uname("r"));
            if (@disk_total_space(_realpath_)) {
                $strReturn .= $this->widgetText($this->getLang("sysinfo_server_diskspace").bytesToString(@disk_total_space(_realpath_)));
                $strReturn .= $this->widgetText($this->getLang("sysinfo_server_diskspacef").bytesToString(@disk_free_space(_realpath_)));
            }
            $strReturn .= $this->widgetSeparator();
        }
        if ($this->getFieldValue("kajona") == "checked") {
            $objManager = new PackagemanagerManager();
            $arrPackageMetadata = $objManager->getAvailablePackages();


            $strReturn .= $this->widgetText($this->getLang("sysinfo_kajona_version")." ".SystemModule::getModuleByName("system")->getStrVersion());
            $strReturn .= $this->widgetText($this->getLang("sysinfo_kajona_nrOfModules")." ".count(SystemModule::getAllModules()));
            $strReturn .= $this->widgetText($this->getLang("sysinfo_kajona_nrOfPackages")." ".count($arrPackageMetadata));
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
        return $this->getLang("sysinfo_name");
    }

    /**
     * @inheritdoc
     */
    public function getWidgetDescription()
    {
        return $this->getLang("sysinfo_description");
    }

    /**
     * @return string
     */
    public function getImgFileName(): string
    {
        return $this->imgFileName;
    }
}

