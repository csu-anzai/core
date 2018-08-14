<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                           *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Model for a language
 *
 * @package module_languages
 * @author sidler@mulchprod.de
 * @targetTable agp_languages.language_id
 *
 * @module languages
 * @moduleId _languages_modul_id_
 */
class LanguagesLanguage extends Model implements ModelInterface, AdminListableInterface
{

    /**
     * @var string
     * @tableColumn agp_languages.language_name
     * @tableColumnDatatype char254
     *
     * @fieldType Kajona\System\Admin\Formentries\FormentryDropdown
     * @fieldDDValues [ar => lang_ar],[bg => lang_bg],[cs => lang_cs],[da => lang_da],[de => lang_de],[el => lang_el],[en => lang_en],[es => lang_es],[fi => lang_fi],[fr => lang_fr],[ga => lang_ga],[he => lang_he],[hr => lang_hr],[hu => lang_hu],[hy => lang_hy],[id => lang_id],[it => lang_it],[ja => lang_ja],[ko => lang_ko],[nl => lang_nl],[no => lang_no],[pl => lang_pl],[pt => lang_pt],[ro => lang_ro],[ru => lang_ru],[sk => lang_sk],[sl => lang_sl],[sv => lang_sv],[th => lang_th],[tr => lang_tr],[uk => lang_uk],[zh => lang_zh]
     * @fieldLabel commons_title
     * @fieldMandatory
     * @fieldValidator Kajona\System\System\Validators\TwocharsValidator
     *
     * @addSearchIndex
     */
    private $strName = "";

    /**
     * @var bool
     * @tableColumn agp_languages.language_default
     * @tableColumnDatatype int
     * @tableColumnIndex
     *
     * @XXXfieldType Kajona\System\Admin\Formentries\FormentryYesno
     * @todo currently hidden
     * @fieldMandatory
     */
    private $bitDefault = false;

    private $strLanguagesAvailable = "ar,bg,cs,da,de,el,en,es,fi,fr,ga,he,hr,hu,hy,id,it,ja,ko,nl,no,pl,pt,ro,ru,sk,sl,sv,th,tr,uk,zh";

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin()
     */
    public function getStrIcon()
    {
        return "icon_language";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo()
    {
        return "";
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription()
    {
        return "";
    }


    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return $this->getLang("lang_".$this->getStrName(), "languages");
    }

    /**
     * Returns the language requested.
     * If the language doesn't exist, false is returned
     *
     * @param string $strName
     *
     * @static
     * @return  LanguagesLanguage|null
     */
    public static function getLanguageByName($strName)
    {

        $objOrmList = new OrmObjectlist();
        $objOrmList->addWhereRestriction(new OrmPropertyCondition("strName", OrmComparatorEnum::Equal(), $strName));
        $arrReturn = $objOrmList->getObjectList(__CLASS__);
        if (count($arrReturn) > 0) {
            return $arrReturn[0];
        } else {
            return null;
        }
    }


    public function setStrName($strName)
    {
        $this->strName = $strName;
    }

    public function setBitDefault($bitDefault)
    {
        $this->bitDefault = $bitDefault;
    }

    public function getStrName()
    {
        return $this->strName;
    }

    public function getBitDefault()
    {
        return $this->bitDefault;
    }

    /**
     * Returns a list of all languages available
     *
     * @return array
     */
    public function getAllLanguagesAvailable()
    {
        return explode(",", $this->strLanguagesAvailable);
    }
}
