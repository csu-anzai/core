<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                              *
********************************************************************************************************/

namespace Kajona\System\Admin;

use Kajona\System\Admin\Formentries\FormentryHidden;
use Kajona\System\System\ArraySectionIterator;
use Kajona\System\System\Carrier;
use Kajona\System\System\Exception;
use Kajona\System\System\LanguagesLanguage;
use Kajona\System\System\Link;
use Kajona\System\System\Model;


/**
 * Admin-class to manage all languages
 *
 * @package module_languages
 * @author sidler@mulchprod.de
 *
 * @module languages
 * @moduleId _languages_modul_id_
 */
class LanguagesAdmin extends AdminSimple implements AdminInterface
{

    public function getOutputModuleNavi()
    {
        $arrReturn = array();
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "list", "", $this->getLang("commons_list"), "", "", true, "adminnavi"));
        return $arrReturn;
    }


    /**
     * Returns a list of the languages
     *
     * @return string
     * @autoTestable
     * @permissions view
     * @throws Exception
     */
    protected function actionList()
    {

        $objArraySectionIterator = new ArraySectionIterator(LanguagesLanguage::getObjectCountFiltered());
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(LanguagesLanguage::getObjectListFiltered(null, "", $objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos()));

        return $this->renderList($objArraySectionIterator);
    }

    protected function renderCopyAction(Model $objListEntry)
    {
        return "";
    }


    /**
     * @return string
     * @permissions edit
     * @throws Exception
     */
    protected function actionEdit()
    {
        return $this->actionNew("edit");
    }

    /**
     * Creates the form to edit an existing language, or to create a new language
     *
     * @param string $strMode
     *
     * @return string
     * @permissions edit
     * @autoTestable
     * @throws Exception
     */
    protected function actionNew($strMode = "new", AdminFormgenerator $objForm = null)
    {

        $objLang = new LanguagesLanguage();
        $arrLanguages = $objLang->getAllLanguagesAvailable();
        $arrLanguagesDD = array();
        foreach ($arrLanguages as $strLangShort) {
            $arrLanguagesDD[$strLangShort] = $this->getLang("lang_".$strLangShort);
        }

        if ($strMode == "new") {
            $objLanguage = new LanguagesLanguage();
        } else {
            $objLanguage = new LanguagesLanguage($this->getSystemid());
            if (!$objLanguage->rightEdit()) {
                return $this->getLang("commons_error_permissions");
            }
        }

        if ($objForm == null) {
            $objForm = $this->getAdminForm($objLanguage);
        }

        $objForm->addField(new FormentryHidden("", "mode"))->setStrValue($strMode);
        return $objForm->renderForm(Link::getLinkAdminHref($this->getArrModule("modul"), "saveLanguage"));
    }

    /**
     * Creates the admin-form object
     *
     * @param LanguagesLanguage $objLanguage
     *
     * @return AdminFormgenerator
     * @throws Exception
     */
    private function getAdminForm(LanguagesLanguage $objLanguage)
    {

        $objLang = new LanguagesLanguage();
        $arrLanguages = $objLang->getAllLanguagesAvailable();
        $arrLanguagesDD = array();
        foreach ($arrLanguages as $strLangShort) {
            $arrLanguagesDD[$strLangShort] = $this->getLang("lang_".$strLangShort);
        }

        $objForm = new AdminFormgenerator("language", $objLanguage);
        $objForm->addDynamicField("strName")->setArrKeyValues($arrLanguagesDD);
        $objForm->addDynamicField("bitDefault");

        return $objForm;
    }

    /**
     * saves the submitted form-data as a new language, oder updates the corresponding language
     *
     * @throws Exception
     * @return string, "" in case of success
     * @permissions edit
     */
    protected function actionSaveLanguage()
    {
        if ($this->getParam("mode") == "new") {
            $objLanguage = new LanguagesLanguage();
        } else {
            $objLanguage = new LanguagesLanguage($this->getSystemid());
            if (!$objLanguage->rightEdit()) {
                return $this->getLang("commons_error_permissions");
            }
        }

        $objForm = $this->getAdminForm($objLanguage);

        if (!$objForm->validateForm()) {
            return $this->actionNew($this->getParam("mode"), $objForm);
        }
        $objForm->updateSourceObject();

        if ($this->getParam("mode") == "new") {
            //language already existing?
            if (LanguagesLanguage::getLanguageByName($objLanguage->getStrName()) !== false) {
                return $this->getLang("language_existing");
            }
        } elseif ($this->getParam("mode") == "edit") {
            $objTestLang = LanguagesLanguage::getLanguageByName($objLanguage->getStrName());
            if ($objTestLang !== false && $objTestLang->getSystemid() != $objLanguage->getSystemid()) {
                return $this->getLang("language_existing");
            }
        }

        $this->objLifeCycleFactory->factory(get_class($objLanguage))->update($objLanguage);
        $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul")));
    }
}
