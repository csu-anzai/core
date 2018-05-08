<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\Admin;

use Kajona\System\System\AbstractController;
use Kajona\System\System\Classloader;
use Kajona\System\System\Exception;
use Kajona\System\System\History;
use Kajona\System\System\LanguagesLanguage;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Link;
use Kajona\System\System\RequestEntrypointEnum;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemModule;

/**
 * The Base-Class for all admin-interface classes.
 * Extend this class (or one of its subclasses) to generate a
 * user interface.
 *
 * The action-method() takes care of calling your action-handlers.
 * If the URL-param "action" is set to "list", the controller calls your
 * action method "actionList()". Return the rendered output, everything else is generated
 * automatically.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @see AdminController::action()
 */
abstract class AdminController extends AbstractController
{

    /**
     * String containing the current module to be used to load texts
     *
     * @var array
     */
    private $arrOutput;

    /**
     * @inject system_admintoolkit
     * @var ToolkitAdmin
     */
    protected $objToolkit;

    /**
     * @inject system_object_builder
     * @var \Kajona\System\System\ObjectBuilder
     */
    protected $objBuilder;

    /**
     * @inject system_resource_loader
     * @var Resourceloader
     */
    protected $objResourceLoader;

    /**
     * @inject system_class_loader
     * @var Classloader
     */
    protected $objClassLoader;

    /**
     * @inject system_life_cycle_factory
     * @var ServiceLifeCycleFactory
     */
    protected $objLifeCycleFactory;

    /**
     * Constructor
     *
     * @param string $strSystemid
     * @throws Exception
     */
    public function __construct($strSystemid = "")
    {
        parent::__construct($strSystemid);

        // default-template: main.tpl
        if ($this->getArrModule("template") == "") {
            $this->setArrModuleEntry("template", "/main.tpl");
        }

//        if ($this->getParam("folderview") != "") {
//            $this->setArrModuleEntry("template", "/folderview.tpl");
//        }

        //set the correct language to the text-object
        $this->getObjLang()->setStrTextLanguage($this->objSession->getAdminLanguage(true));
    }

    /**
     * Returns the data for a registered module
     * FIXME: validate if still required
     *
     * @param string $strName
     * @param bool $bitCache
     *
     * @return mixed
     * @deprecated
     */
    public function getModuleData($strName, $bitCache = true)
    {
        return SystemModule::getPlainModuleData($strName, $bitCache);
    }

    /**
     * Returns the SystemID of a installed module
     *
     * @param string $strModule
     *
     * @return string "" in case of an error
     * @deprecated
     */
    public function getModuleSystemid($strModule)
    {
        $objModule = SystemModule::getModuleByName($strModule);
        if ($objModule != null) {
            return $objModule->getSystemid();
        } else {
            return "";
        }
    }

    /**
     * Creates a text-based description of the current module.
     * Therefore the text-entry module_description should be available.
     *
     * @return string
     * @since 3.2.1
     */
    public function getModuleDescription()
    {
        $strDesc = $this->getLang("module_description");
        if ($strDesc != "!module_description!") {
            return $strDesc;
        } else {
            return "";
        }
    }

    // --- HistoryMethods -----------------------------------------------------------------------------------

    /**
     * Returns the URL at the given position (from HistoryArray)
     *
     * @param int $intPosition
     *
     * @deprecated use History::getAdminHistory() instead
     * @see History::getAdminHistory()
     * @return string
     */
    protected function getHistory($intPosition = 0)
    {
        $objHistory = new History();
        return $objHistory->getAdminHistory($intPosition);
    }


    // --- OutputMethods ------------------------------------------------------------------------------------

    /**
     * Hook-method to modify some parts of the rendered content right before rendered into the template.
     * May also be used to add additional elements to the array rendered into
     * the admin-template.
     *
     * @param array &$arrContent
     *
     * @return void
     * @deprecated
     */
    protected function onRenderOutput(&$arrContent)
    {
    }

    /**
     * Validates if the requested module is valid for the current aspect.
     * If necessary, the current aspect is updated.
     *
     * @return void
     */
    private function validateAndUpdateCurrentAspect()
    {
        if (ResponseObject::getInstance()->getObjEntrypoint()->equals(RequestEntrypointEnum::XML()) || $this->getArrModule("template") == "/folderview.tpl") {
            return;
        }

        $objModule = $this->getObjModule();
        $strCurrentAspect = SystemAspect::getCurrentAspectId();
        if ($objModule != null && $objModule->getStrAspect() != "") {
            $arrAspects = explode(",", $objModule->getStrAspect());
            if (count($arrAspects) == 1 && $arrAspects[0] != $strCurrentAspect) {
                $objAspect = new SystemAspect($arrAspects[0]);
                if ($objAspect->rightView()) {
                    SystemAspect::setCurrentAspectId($arrAspects[0]);
                }
            }

        }
    }

    /**
     * Tries to generate a quick-help button.
     * Tests for exisiting help texts
     *
     * @return string
     */
    public function getQuickHelp()
    {
        $strReturn = "";

        $strTextname = $this->objLang->stringToPlaceholder("quickhelp_".$this->getAction());
        $strText = $this->getLang($strTextname);

        if ($strText != "!".$strTextname."!") {
            //Text found, embed the quickhelp into the current skin
            $strReturn .= $this->objToolkit->getQuickhelp($strText);
        }

        return $strReturn;
    }

    /**
     * @return array
     */
    public function getArrOutputNaviEntries()
    {
        $arrReturn = [Link::getLinkAdmin("dashboard", "", "", $this->getLang("modul_titel", "dashboard"))];

        if ($this->getObjModule()->rightView()) {
            $arrReturn[] = Link::getLinkAdmin($this->getArrModule("modul"), "", "", $this->getOutputModuleTitle());
        }

        //see, if the current action may be mapped
        $strActionName = $this->getObjLang()->stringToPlaceholder("action_".$this->getAction());
        $strAction = $this->getLang($strActionName);
        if ($strAction != "!".$strActionName."!") {
            $arrReturn[] = $strAction;
        }

        return $arrReturn;
    }

    /**
     * Writes the ModuleNavi, overwrite if needed
     * Use two-dim arary:
     * array[
     *     array["right", "link"],
     *     array["right", "link"]
     * ]
     *
     * @return array array containing all links
     */
    public function getOutputModuleNavi()
    {
        return array();
    }

    /**
     * Renders the "always present" module permissions entry for each module (takes the currents' user permissions into
     * account).
     * If you don't want this default behaviour, overwrite this method.
     *
     * @return array
     */
    public function getModuleRightNaviEntry()
    {
        $arrLinks = array();
        $arrLinks[] = array("", "");
        $arrLinks[] = array("right", Link::getLinkAdmin("right", "change", "&systemid=".$this->getObjModule()->getStrSystemid(), $this->getLang("commons_module_permissions")));
        return $arrLinks;
    }

    /**
     * Writes the ModuleTitle, overwrite if needed
     *
     * @return string
     */
    protected function getOutputModuleTitle()
    {
        if ($this->getLang("modul_titel") != "!modul_titel!") {
            return $this->getLang("modul_titel");
        } else {
            return $this->getArrModule("modul");
        }
    }

    /**
     * Creates the action name to be rendered in the output, in most cases below the pathnavigation-bar
     *
     * @return string
     */
    protected function getOutputActionTitle()
    {
        return $this->getOutputModuleTitle();
    }

    /**
     * Writes the SessionInfo, overwrite if needed
     *
     * @return string
     */
    protected function getOutputLogin()
    {
        $objLogin = $this->objBuilder->factory("Kajona\\System\\Admin\\LoginAdmin");
        return $objLogin->getLoginStatus();
    }

    /**
     * Use this method to reload a specific url.
     * <b>Use ONLY this method and DO NOT use header("Location: ...");</b>
     *
     * @param string $strUrlToLoad
     *
     * @return void
     */
    public function adminReload($strUrlToLoad)
    {
        //filling constants
        $strUrlToLoad = str_replace("_webpath_", _webpath_, $strUrlToLoad);
        $strUrlToLoad = str_replace("_indexpath_", _indexpath_, $strUrlToLoad);
        //No redirect, if close-Command for admin-area should be sent
        if ($this->getParam("peClose") == "") {
            ResponseObject::getInstance()->setStrRedirectUrl($strUrlToLoad);
        }
    }

    /**
     * Loads the language to edit content
     *
     * @return string
     * @deprecated use LanguagesLanguage directly
     */
    public function getLanguageToWorkOn()
    {
        $objLanguage = new LanguagesLanguage();
        return $objLanguage->getAdminLanguage();
    }

}

