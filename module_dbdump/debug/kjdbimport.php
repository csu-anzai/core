<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Debugging\Debug;

use Kajona\Dbdump\System\DbImport;
use Kajona\System\System\Carrier;
use Kajona\System\System\Filesystem;
use Kajona\System\System\StringUtil;

if (issetPost("doimport")) {
    $strFilename = getPost("dumpname");
    $objImporter = new DbImport(Carrier::getInstance()->getObjDB(), true);
    echo "importing ".$strFilename."\n";
    if ($objImporter->importFile("/project/dbdumps/".$strFilename)) {
        echo "\n<span style='color: green;font-weight:bold;'>import successfull.</span>\n";
    } else {
        echo "\n<span style='color: red;font-weight:bold;'>import failed!!</span>\n";
    }

} else {
    echo "Searching for dumps in dbdumps under: "._projectpath_."\n";

    $objFilesystem = new Filesystem();
    if ($objFilesystem->isWritable("/project/temp")) {
        echo "Searching dbdumps available...\n";

        $arrFiles = $objFilesystem->getFilelist(_projectpath_."/dbdumps/", [".zip"]);
        echo "Found ".count($arrFiles)." dump(s)\n\n";

        echo "<form method='post'>";
        echo "Dump to import:\n";

        $arrImportfileData = [];
        foreach ($arrFiles as $strOneFile) {
            if (StringUtil::indexOf($strOneFile, "_kj_") === false) {
                continue;
            }

            $arrDetails = $objFilesystem->getFileDetails(_projectpath_."/dbdumps/".$strOneFile);

            $strTimestamp = "";
            if (StringUtil::indexOf($strOneFile, "_") !== false) {
                $strTimestamp = StringUtil::substring($strOneFile, StringUtil::lastIndexOf($strOneFile, "_") + 1, (StringUtil::indexOf($strOneFile, ".") - StringUtil::lastIndexOf($strOneFile, "_")));
            }

            $strFileInfo = $strOneFile
                ." (".bytesToString($arrDetails['filesize']).")"
                .(StringUtil::length($strTimestamp) > 9 && is_numeric($strTimestamp) ? "\n    Timestamp according to file name: ".timeToString($strTimestamp) : "").""
                ."\n    Timestamp according to file info: ".timeToString($arrDetails['filechange']);

            $arrImportfileData[$strOneFile] = $strFileInfo;
        }

        $bitShowButton = false;
        foreach ($arrImportfileData as $strFilename => $strFileInfo) {
            echo "\n<input type='radio' name='dumpname' id='dumpname_".$strFilename."' value='$strFilename' /><label for='dumpname_".$strFilename."'>".$strFileInfo."</label>";
            $bitShowButton = true;
        }

        if ($bitShowButton) {
            echo "\n\n<input type='hidden' name='doimport' value='1' />";
            echo "<input type='submit' value='Import dump' />";
        } else {
            echo "\nNo dump found.";
        }

        echo "</form>";
    } else {
        echo "<span style='color: red;'>WARNING!!\n\nThe folder project/temp is NOT writeable. DB dumps can NOT be imported! </span>\n\n";
    }


}


