<?php

$strBasePath = __DIR__ . "/../";
$arrModules = scandir($strBasePath);
$strIndex = "# Index\n";

foreach ($arrModules as $strModule) {
    $strDocPath = $strBasePath . $strModule . "/docs";
    if ($strModule[0] != "." && $strModule[0] != "_" && is_dir($strDocPath)) {
        $strIndex.= "\n";
        $strIndex.= "## {$strModule}\n";
        if (!is_dir(__DIR__ . "/{$strModule}")) {
            mkdir(__DIR__ . "/{$strModule}");
        }
        $arrDocs = scandir($strDocPath);
        foreach ($arrDocs as $strFile) {
            $strPath = $strDocPath . "/" . $strFile;
            if ($strFile[0] != "." && is_file($strPath)) {
                $strName = pathinfo($strFile, PATHINFO_FILENAME);
                $strIndex.= "* [{$strName}]({$strModule}/{$strFile})\n";
                copy($strPath, __DIR__ . "/{$strModule}/{$strFile}");
            }
        }
        $strIndex.= "\n";
    }
}

file_put_contents(__DIR__ . "/index.md", $strIndex);
