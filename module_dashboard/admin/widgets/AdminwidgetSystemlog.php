<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$		                        *
********************************************************************************************************/

namespace Kajona\Dashboard\Admin\Widgets;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryText;
use Kajona\System\System\Carrier;
use Kajona\System\System\Filesystem;
use Kajona\System\System\SystemModule;

/**
 * @package module_dashboard
 *
 */
class AdminwidgetSystemlog extends Adminwidget implements AdminwidgetInterface
{

    /**
     * Basic constructor, registers the fields to be persisted and loaded
     *
     */
    public function __construct()
    {
        parent::__construct();
        //register the fields to be persisted and loaded
        $this->setPersistenceKeys(array("nrofrows"));
    }

    /**
     * Allows the widget to add additional fields to the edit-/create form.
     * Use the toolkit class as usual.
     *
     * @return string
     */
    public function getEditForm()
    {
        $strReturn = "";
        $strReturn .= $this->objToolkit->formInputText("nrofrows", $this->getLang("syslog_nrofrows"), $this->getFieldValue("nrofrows"));
        return $strReturn;
    }

    /**
     * @param AdminFormgenerator $form
     */
    public function getEditFormContent(AdminFormgenerator $form)
    {
        $form->addField(new FormentryText("nrofrows", ""), "")
            ->setStrLabel($this->getLang("syslog_nrofrows"))
            ->setStrValue($this->getFieldValue("nrofrows"));
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

        if (!SystemModule::getModuleByName("system")->rightRight3() || !Carrier::getInstance()->getObjSession()->isSuperAdmin()) {
            return $this->getLang("commons_error_permissions");
        }

        if ($this->getFieldValue("nrofrows") == "") {
            return $this->getEditWidgetForm();
        }

        $objFilesystem = new Filesystem();
        $arrFiles = $objFilesystem->getFilelist(_projectpath_."/log", array(".log"));

        foreach ($arrFiles as $strName) {
            $objFilesystem->openFilePointer(_projectpath_."/log/".$strName, "r");
            $strLogContent = $objFilesystem->readLastLinesFromFile($this->getFieldValue("nrofrows"));
            $objFilesystem->closeFilePointer();

            $strLogContent = str_replace(array("INFO", "ERROR"), array("INFO   ", "ERROR  "), $strLogContent);
            $arrLogEntries = explode("\r", $strLogContent);
            $strReturn .= $this->objToolkit->getPreformatted($arrLogEntries);

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
        return $this->getLang("syslog_name");
    }

    /**
     * @inheritdoc
     */
    public function getWidgetDescription()
    {
        return $this->getLang("syslog_description");
    }

    public function getWidgetImg()
    {
        return "/files/extract/widgets/systemlog.png";
        //return Resourceloader::getInstance()->getWebPathForModule("module_dashboard")."/img/widgets/systeminfo.png";
    }
}

