<?php

$strBasePath = __DIR__ . "/../../";
$arrModules = scandir($strBasePath);
$strIndex = "# Index\n";

foreach ($arrModules as $strModule) {
    $strDocPath = $strBasePath . $strModule . "/docs";
    if ($strModule[0] != "." && $strModule[0] != "_" && is_dir($strDocPath)) {

//        if (!is_dir(__DIR__ . "/{$strModule}")) {
//            mkdir(__DIR__ . "/{$strModule}");
//        }

        $arrDocs = array_filter(scandir($strDocPath), function($strFile) {
            return $strFile[0] != "." && substr($strFile, -3) == ".md";
        });

        if (count($arrDocs) > 0) {

            $strIndex.= "\n";
            $strIndex.= "## {$strModule}\n";

            foreach ($arrDocs as $strFile) {
                $strPath = $strDocPath."/".$strFile;
                $strName = pathinfo($strFile, PATHINFO_FILENAME);
                $strIndex .= "* [{$strName}](../../{$strModule}/docs/{$strFile})\n";
//            copy($strPath, __DIR__ . "/{$strModule}/{$strFile}");
            }
            $strIndex.= "\n";
        }
    }
}

file_put_contents(__DIR__."/overview.md", $strIndex);
