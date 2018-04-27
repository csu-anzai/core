<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\Installer;

use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\SamplecontentInstallerInterface;
use Kajona\System\System\SystemSetting;

/**
 * Installer of the mediamanager samplecontent
 */
class InstallerSamplecontentMediamanager implements SamplecontentInstallerInterface
{

    private $objDB;
    private $strContentLanguage;

    /**
     * @inheritDoc
     */
    public function isInstalled()
    {
        return validateSystemid(SystemSetting::getConfigValue("_mediamanager_default_filesrepoid_"));
    }


    /**
     * Does the hard work: installs the module and registers needed constants
     *
     * @return string
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function install()
    {
        $strReturn = "";

        $strReturn .= "Creating picture upload folder\n";
        if (!is_dir(_realpath_._filespath_."/images/upload")) {
            mkdir(_realpath_._filespath_."/images/upload", 0777, true);
        }

        $strReturn .= "Creating new picture repository\n";
        $objRepo = new MediamanagerRepo();

        if ($this->strContentLanguage == "de") {
            $objRepo->setStrTitle("Hochgeladene Bilder");
        }
        else {
            $objRepo->setStrTitle("Picture uploads");
        }

        $objRepo->setStrPath(_filespath_."/images/upload");
        $objRepo->setStrUploadFilter(".jpg,.png,.gif,.jpeg");
        $objRepo->setStrViewFilter(".jpg,.png,.gif,.jpeg");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objRepo))->update($objRepo);
        $objRepo->syncRepo();

        $strReturn .= "ID of new repo: ".$objRepo->getSystemid()."\n";

        $strReturn .= "Setting the repository as the default images repository\n";
        $objSetting = SystemSetting::getConfigByName("_mediamanager_default_imagesrepoid_");
        $objSetting->setStrValue($objRepo->getSystemid());
        $objSetting->updateObjectToDb();

        $strReturn .= "Creating new file repository\n";
        $objRepo = new MediamanagerRepo();

        if ($this->strContentLanguage == "de") {
            $objRepo->setStrTitle("Hochgeladene Dateien");
        }
        else {
            $objRepo->setStrTitle("File uploads");
        }

        $objRepo->setStrPath(_filespath_."/downloads/default");
        $objRepo->setStrUploadFilter(".zip,.pdf,.txt");
        $objRepo->setStrViewFilter(".zip,.pdf,.txt");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objRepo))->update($objRepo);
        $objRepo->syncRepo();
        $strReturn .= "ID of new repo: ".$objRepo->getSystemid()."\n";

        $strReturn .= "Setting the repository as the default files repository\n";
        $objSetting = SystemSetting::getConfigByName("_mediamanager_default_filesrepoid_");
        $objSetting->setStrValue($objRepo->getSystemid());
        $objSetting->updateObjectToDb();


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
        return "mediamanager";
    }

}
