#!/usr/bin/php
<?php

echo "compiling and minifying skin css files".PHP_EOL;

echo "Collecting less files".PHP_EOL;

$strRoot = realpath(__DIR__."/../../..");
$arrFiles = [
    $strRoot."/core/module_v4skin/admin/skins/kajona_v4/less/bootstrap.less"
];


//search less folders
$objIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($strRoot, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST,
    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
);

$arrIncludedModules = [];
if (is_file($strRoot."/project/packageconfig.php")) {
    include $strRoot."/project/packageconfig.php";
}
if (!isset($arrExcludedModules["core"])) {
    $arrExcludedModules["core"] = [];
}
$arrExcludedModules["core"][] = "_buildfiles";
$arrExcludedModules["core"][] = "module_installer";
$arrExcludedModules["core"][] = "module_v4skin";

$arrFolders = [];
foreach ($objIterator as $strPath => $objDir) {
    $strTestPath = str_replace([$strRoot.DIRECTORY_SEPARATOR, "\\"], ["", "/"], $strPath);
    $arrPath = explode("/", $strTestPath);

    if (count($arrPath) > 2) {
        //defined as included?
        if (array_key_exists($arrPath[0], $arrIncludedModules)) {
            if (!in_array($arrPath[1], $arrIncludedModules[$arrPath[0]])) {
                continue;
            }
        }

        //defined as excluded?
        if (array_key_exists($arrPath[0], $arrExcludedModules)) {
            if (in_array($arrPath[1], $arrExcludedModules[$arrPath[0]])) {
                continue;
            }
        }
    } else {
        continue;
    }

    if ($objDir->getFilename() == "less" && $objDir->isDir()) {
        $arrFolders[] = $strPath;
        //fetch all less files inside
        foreach (scandir($strPath) as $strFile) {
            if (substr($strFile, -5) == ".less") {
                $arrFiles[] = $strPath.DIRECTORY_SEPARATOR.$strFile;
            }
        }

    }
}

//create a temp less file
$strFile = "";
foreach ($arrFiles as $strLess) {
    //make it relative
    $strLess = str_replace([$strRoot."/", $strRoot.DIRECTORY_SEPARATOR, "\\"], ["", "", "/"], $strLess);
    $strLess = "../../../../../../".$strLess;
    $strFile .= "  @import \"".$strLess."\";".PHP_EOL;
}

echo "Temp file:".PHP_EOL.$strFile;

file_put_contents($strRoot."/core/module_v4skin/admin/skins/kajona_v4/less/styles.less", $strFile);

//files to compile
$arrFilesToCompile = array(
    __DIR__."/../../module_v4skin/admin/skins/kajona_v4/less/styles.less" => __DIR__."/../../module_v4skin/admin/skins/kajona_v4/less/styles.min.css"
);

foreach ($arrFilesToCompile as $strSourceFile => $strTargetFile) {
    if (is_file($strSourceFile)) {
        echo "Compiling ".$strSourceFile.PHP_EOL;
        $strLessBin = "node " . __DIR__ . "/../jstests/node_modules/less/bin/lessc";
        system($strLessBin . " --verbose " . escapeshellarg($strSourceFile) . " " . escapeshellarg($strTargetFile));

        echo "Minifiying ".$strTargetFile.PHP_EOL;
        $strMinifyBin = "node " . __DIR__ . "/../jstests/node_modules/clean-css/bin/cleancss";
        system($strMinifyBin . " -o ". escapeshellarg($strTargetFile)." ". escapeshellarg($strTargetFile));

    } else {
        echo "Skipping ".$strSourceFile.", not existing".PHP_EOL;
    }
}

unlink($strRoot."/core/module_v4skin/admin/skins/kajona_v4/less/styles.less");
echo "Done.".PHP_EOL;