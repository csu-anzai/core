<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\Debugging\Debug;

use Kajona\System\System\Filesystem;
use Kajona\System\System\StringUtil;

if (issetPost("doimport")) {
    $strFilename = getPost("dumpname");
    $objDb = \Kajona\System\System\Carrier::getInstance()->getObjDB();
    echo "importing " . $strFilename . "\n";
    if ($objDb->importDb($strFilename, true)) {
        echo "\n<span style='color: green;font-weight:bold;'>import successfull.</span>\n";
    } else {
        echo "\n<span style='color: red;font-weight:bold;'>import failed!!</span>\n";
    }
} else {
    echo "Searching for dumps in dbdumps under: " . _projectpath_ . "\n";

    $objFilesystem = new Filesystem();
    if ($objFilesystem->isWritable("/project/dbdumps") && $objFilesystem->isWritable("/project/temp")) {
        echo "Searching dbdumps available...\n";

        $arrFiles = $objFilesystem->getFilelist(_projectpath_ . "/dbdumps/", array(".zip", ".gz", ".sql"));
        echo "Found " . count($arrFiles) . " dump(s)\n\n";

        echo "<form method='post'>";
        echo "Dump to import:\n";

        $arrImportfileData = array();
        foreach ($arrFiles as $strOneFile) {
            $arrDetails = $objFilesystem->getFileDetails(_projectpath_ . "/dbdumps/" . $strOneFile);

            $strTimestamp = "";
            if (StringUtil::indexOf($strOneFile, "_") !== false) {
                $strTimestamp = StringUtil::substring($strOneFile, StringUtil::lastIndexOf($strOneFile, "_") + 1, (StringUtil::indexOf($strOneFile, ".") - StringUtil::lastIndexOf($strOneFile, "_")));
            }


            $strFileInfo = $strOneFile
                . " (" . bytesToString($arrDetails['filesize']) . ")"
                . (StringUtil::length($strTimestamp) > 9 && is_numeric($strTimestamp) ? "\n    Timestamp according to file name: ".timeToString($strTimestamp) : "").""
                . "\n    Timestamp according to file info: " . timeToString($arrDetails['filechange']);

            $arrImportfileData[$strOneFile] = $strFileInfo;
        }

        $bitShowButton = false;
        foreach ($arrImportfileData as $strFilename => $strFileInfo) {
            echo "\n<input type='radio' name='dumpname' id='dumpname_" . $strFilename . "' value='$strFilename' /><label for='dumpname_" . $strFilename . "'>" . $strFileInfo . "</label>";
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
        echo "<span style='color: red;'>WARNING!!\n\nThe folder /project/dbdumps or /project/temp is NOT writeable. DB dumps can NOT be imported! </span>\n\n";
    }

}


echo "\n\n";
echo "+-------------------------------------------------------------------------------+\n";
echo "| (c) www.kajona.de                                                             |\n";
echo "+-------------------------------------------------------------------------------+\n";


