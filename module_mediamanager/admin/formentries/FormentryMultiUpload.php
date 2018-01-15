<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\Admin\Formentries;

use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\Mediamanager\System\Validators\MediamanagerUploadValidator;
use Kajona\System\Admin\Formentries\FormentryBase;
use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Reflection;
use Kajona\System\System\SystemModule;
use Kajona\System\System\SystemSession;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\Validators\DummyValidator;
use Kajona\System\System\Validators\SystemidValidator;


/**
 * Integrates the multi-upload into a single form, queries the mediamanager for storing uploads.
 * The mapped database-field is a systemid, so make sure to have at least a varchar20 field available.
 *
 * @author sidler@mulchprod.de
 * @since 6.5
 */
class FormentryMultiUpload extends FormentryBase implements FormentryPrintableInterface
{

    const STR_MM_REPOID_ANNOTATION = "@fieldMMRepoIdConstant";

    private $strRepoId = "";


    /**
     * @inheritDoc
     */
    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null, $strRepoId = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        //default: files-repo-id
        if ($strRepoId !== null) {
            $this->strRepoId = $strRepoId;
        } else {
            $this->strRepoId = SystemSetting::getConfigValue("_mediamanager_default_filesrepoid_");
        }

        //may be overwritten with a dedicated repo-id
        if ($this->getObjSourceObject() != null && $this->getStrSourceProperty() != "") {
            $objReflection = new Reflection($this->getObjSourceObject());
            //try to find the matching source property
            $strSourceProperty = $this->getCurrentProperty(self::STR_MM_REPOID_ANNOTATION);
            if ($strSourceProperty != null) {
                $strId = $objReflection->getAnnotationValueForProperty($strSourceProperty, self::STR_MM_REPOID_ANNOTATION);
                if ($strId !== null) {
                    $this->strRepoId = SystemSetting::getConfigValue($strId);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getObjValidator()
    {
        return new MediamanagerUploadValidator($this->strRepoId, $this->getBitMandatory());
    }


    /**
     * Renders the field itself.
     * In most cases, based on the current toolkit.
     *
     * @return string
     */
    public function renderField()
    {
        $objToolkit = Carrier::getInstance()->getObjToolkit("admin");
        $strReturn = "";

        if (empty($this->getStrValue())) {
            $this->setStrValue(generateSystemid());
        }

        /** @var MediamanagerRepo $objRepo */
        $objRepo = Objectfactory::getInstance()->getObject($this->strRepoId);

        if ($objRepo === null) {
            return $objToolkit->warningBox("No mediamanager repo set");
        }

        //place the upload-repo id as a hidden form entry
        $strReturn .= $objToolkit->formInputHidden($this->getStrEntryName()."_id", $this->getStrValue());

        if ($this->getStrHint() != null) {
            $strReturn .= $objToolkit->formTextRow($this->getStrHint());
        }

        //and render the multiupload fields
        $strReturn .= $objToolkit->formInputUploadInline($this->getStrEntryName(), $this->getStrLabel(), $objRepo, $this->getStrValue(), $this->getBitReadonly());

        return $strReturn;
    }


    /**
     * Overwritten base method, processes the hidden fields, too.
     */
    protected function updateValue()
    {
        $arrParams = Carrier::getAllParams();
        if (isset($arrParams[$this->getStrEntryName()."_id"])) {
            $this->setStrValue($arrParams[$this->getStrEntryName()."_id"]);
        } else {
            $this->setStrValue($this->getValueFromObject());
        }
    }

    /**
     * @inheritdoc
     */
    public function getValueAsText()
    {

        /** @var MediamanagerRepo $objRepo */
        $objRepo = Objectfactory::getInstance()->getObject($this->strRepoId);
        $objMMFile = MediamanagerFile::getFileForPath($this->strRepoId, $objRepo->getStrPath()."/".$this->getStrValue());

        $arrLinks = [];
        if ($objMMFile != null) {
            /** @var MediamanagerFile $objFile */
            foreach (MediamanagerFile::getObjectListFiltered(null, $objMMFile->getSystemid()) as $objFile) {
                $arrLinks[] = "<a href='"._webpath_."/download.php?systemid=".$objFile->getSystemid()."'>".$objFile->getStrName()."</a>";
            }
        }

        return implode("\r\n<br />", $arrLinks);
    }

    /**
     * @param string $strRepoId
     */
    public function setStrRepoId(string $strRepoId)
    {
        $this->strRepoId = $strRepoId;
    }


}
