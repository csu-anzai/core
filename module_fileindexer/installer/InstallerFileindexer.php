<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                            *
********************************************************************************************************/

namespace Kajona\Fileindexer\Installer;

use Kajona\System\System\InstallerBase;
use Kajona\System\System\InstallerInterface;
use Kajona\System\System\SystemModule;

/**
 * InstallerFileindexer
 *
 * @package module_fileindexer
 * @moduleId _fileindexer_module_id_
 */
class InstallerFileindexer extends InstallerBase implements InstallerInterface
{
    public function install()
    {
        $strReturn = "Installing ".$this->objMetadata->getStrTitle()."...\n";

        //register the module
        $this->registerModule(
            "fileindexer",
            _fileindexer_module_id_,
            "",
            "",
            $this->objMetadata->getStrVersion(),
            false
        );

        return $strReturn;
    }

    public function update()
    {
        $strReturn = "";
        //check installed version and to which version we can update
        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);

        $strReturn .= "Version found:\n\t Module: ".$arrModule["module_name"].", Version: ".$arrModule["module_version"]."\n\n";

        return $strReturn."\n\n";
    }
}
