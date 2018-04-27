<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Flow\Installer;

use Kajona\System\System\SamplecontentInstallerInterface;

/**
 * Installer
 */
class InstallerSamplecontentFlow implements SamplecontentInstallerInterface
{
    public function isInstalled()
    {
        return true;
    }

    public function install()
    {

    }

    public function setObjDb($objDb)
    {
    }

    public function setStrContentlanguage($strContentlanguage)
    {
    }

    public function getCorrespondingModule()
    {
        return "flow";
    }
}

