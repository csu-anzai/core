<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\Debugging\Debug;

use Kajona\System\System\Carrier;
use Kajona\System\System\Config;
use Kajona\System\System\StringUtil;

$objDb = Carrier::getInstance()->getObjDB();
if (issetPost("doexport")) {
    echo "Exporting Database \n";

    if (issetPost("useinternal")) {
        Config::getInstance()->setConfig("dbexport", "internal");
    }

    $arrExcludedTables = array_diff($objDb->getTables(), array_keys(getPost("table")));

    echo "Excluded: ".implode(", ", $arrExcludedTables).PHP_EOL;
    echo "Export driver: ".Config::getInstance()->getConfig("dbexport").PHP_EOL;

    if ($objDb->dumpDb($arrExcludedTables, true)) {
        echo "\n<span style='color: green;font-weight:bold;'>export successful.</span>\n";
    } else {
        echo "\n<span style='color: red;font-weight:bold;'>export failed!!</span>\n";
    }

} else {
    echo "<form method='post'>";

    echo "<input type='hidden' name='doexport' value='1' />";
    echo "<input type='checkbox' value='useinternal' id='useinternal' /><label for='useinternal'>Force use of internal dbdump module</label>".PHP_EOL;
    echo "<input type='submit' value='Create dump' />".PHP_EOL.PHP_EOL;

    echo "Tables to be included in dump:".PHP_EOL.PHP_EOL;
    foreach ($objDb->getTables() as $strTable) {
        $strChecked = "checked='checked'";

        $arrDisabled = [
            _dbprefix_ . "search_ix_content",
            _dbprefix_ . "search_ix_document",
            _dbprefix_ . "workflows_stat_wfc",
            _dbprefix_ . "workflows_stat_wfh",
            _dbprefix_ . "workflows_user_log",
            _dbprefix_ . "session",
        ];

        if (in_array($strTable, $arrDisabled) || StringUtil::indexOf($strTable, "_partition_") !== false) {
            $strChecked = "";
        }

        echo "<input type='checkbox' value='table[{$strTable}]' name='table[{$strTable}]' id='table[{$strTable}]' {$strChecked} /><label for='table[{$strTable}]'>{$strTable}</label>".PHP_EOL;
    }

    echo "</form>";

}



echo "\n\n";
echo "+-------------------------------------------------------------------------------+\n";
echo "| (c) www.kajona.de                                                             |\n";
echo "+-------------------------------------------------------------------------------+\n";


