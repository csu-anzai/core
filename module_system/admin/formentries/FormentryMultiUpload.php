<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Reflection;
use Kajona\System\System\SystemModule;
use Kajona\System\System\SystemSession;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\Validators\DummyValidator;


/**
 * Integrates the multi-upload into a single form, queries the mediamanager for storing uploads.
 * The mapped database-field is a systemid, so make sure to have at least a varchar20 field available.
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 */
class FormentryMultiUpload extends FormentryBase implements FormentryPrintableInterface
{


    private $strRepoId = "";

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




        //$strReturn .= $objToolkit->formInputUpload($this->getStrEntryName(), $this->getStrLabel(), "", $strFile, $strFileHref, !$this->getBitReadonly());

        if (empty($this->getStrValue())) {
            $this->setStrValue(generateSystemid());
        }

        /** @var MediamanagerRepo $objRepo */
        $objRepo = Objectfactory::getInstance()->getObject($this->strRepoId) ?? Objectfactory::getInstance()->getObject(SystemSetting::getConfigValue("_mediamanager_default_filesrepoid_"));

        //place the upload-repo id as a hidden form entry
        $strReturn .= $objToolkit->formInputHidden($this->getStrEntryName(), $this->getStrValue());

        if ($this->getStrHint() != null) {
            $strReturn .= $objToolkit->formTextRow($this->getStrHint());
        }
        //and render the multiupload fields
        $strReturn .= $objToolkit->formInputUploadInline($this->getStrEntryName()."_ul", $this->getStrLabel(), $objRepo, $this->getStrValue());

        return $strReturn;
    }



    public function getValueAsText()
    {
        list($strFile, $strFileHref) = $this->getFileNameAndHref();

        if (!empty($strFile)) {
            return '<a href="' . $strFileHref . '">' . $strFile . '</a>';
        } else {
            return '-';
        }
    }



}
