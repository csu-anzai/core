<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/
namespace Kajona\System\Installer;

use Kajona\System\System\Carrier;
use Kajona\System\System\Database;
use Kajona\System\System\LanguagesLanguage;
use Kajona\System\System\SamplecontentInstallerInterface;
use Kajona\System\System\SystemModule;


/**
 * Installer of the navigation languages
 *
 */
class InstallerSamplecontentZZLanguages implements SamplecontentInstallerInterface
{

    /**
     * @var Database
     */
    private $objDB;
    private $strContentLanguage;


    /**
     * @inheritDoc
     */
    public function isInstalled()
    {
        if(SystemModule::getModuleByName("pages") == null) {
            return true;
        }

        $strCountQuery = "SELECT COUNT(*) AS cnt
                                FROM "._dbprefix_."page_element
                               WHERE page_element_ph_language = ''";
        $arrCount = Carrier::getInstance()->getObjDB()->getPRow($strCountQuery, array());
        return $arrCount["cnt"] == 0;
    }


    /**
     *
     * Does the hard work: installs the module and registers needed constants
     *
     * @return string
     */
    public function install()
    {
        $strReturn = "";

        $strReturn .= "Assigning null-properties and elements to the default language.\n";
        if ($this->strContentLanguage == "de") {

            $strReturn .= " Target language: de\n";

            $objLang = new LanguagesLanguage();
            $objLang->setStrAdminLanguageToWorkOn("de");
        }
        else {

            $strReturn .= " Target language: en\n";

            $objLang = new LanguagesLanguage();
            $objLang->setStrAdminLanguageToWorkOn("en");

        }


        return $strReturn;
    }

    public function setObjDb($objDb)
    {
        $this->objDB = $objDb;
    }

    public function setStrContentlanguage($strContentlanguage)
    {
        $this->strContentLanguage = $strContentlanguage;
    }

    public function getCorrespondingModule()
    {
        return "languages";
    }
}
