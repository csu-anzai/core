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

$arrFolders = [];
foreach ($objIterator as $strPath => $objDir) {

    if (strpos($strPath, "core/_buildfiles") !== false) {
        continue;
    }

    if (strpos($strPath, "core/module_installer") !== false) {
        continue;
    }

    if (strpos($strPath, "core/module_v4skin") !== false) {
        continue;
    }

    if ($objDir->getFilename() == "less" && $objDir->isDir()) {
        $arrFolders[] = $strPath;
        //fetch all less files inside
        foreach (scandir($strPath) as $strFile) {
            if (substr($strFile, -5) == ".less") {
                $arrFiles[] = $strPath."/".$strFile;
            }
        }

    }
}

//create a temp less file
$strFile = "";
foreach ($arrFiles as $strLess) {
    //make it relative
    $strLess = str_replace($strRoot."/", "", $strLess);
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
        $strMinifyBin = "node " . __DIR__ . "/../jstests/node_modules/.bin/cleancss";
        system($strMinifyBin . " -o ". escapeshellarg($strTargetFile)." ". escapeshellarg($strTargetFile));

    } else {
        echo "Skipping ".$strSourceFile.", not existing".PHP_EOL;
    }
}

unlink($strRoot."/core/module_v4skin/admin/skins/kajona_v4/less/styles.less");
echo "Done.".PHP_EOL;