#!/usr/bin/php
<?php

echo "compiling and minifying installer css files".PHP_EOL;

//files to compile
$arrFilesToCompile = array(
    __DIR__."/../../module_installer/less/bootstrap.less" => __DIR__."/../../module_installer/less/styles.min.css"
);

foreach($arrFilesToCompile as $strSourceFile => $strTargetFile) {
    if (is_file($strSourceFile)) {
        echo "compiling ".$strSourceFile.PHP_EOL;
        $strLessBin = "node " . __DIR__ . "/../jstests/node_modules/less/bin/lessc";
        system($strLessBin . " --verbose " . escapeshellarg($strSourceFile) . " " . escapeshellarg($strTargetFile));

        echo "Minifiying ".$strTargetFile.PHP_EOL;
        $strMinifyBin = "node " . __DIR__ . "/../jstests/node_modules/clean-css-cli/bin/cleancss";
        system($strMinifyBin . " -o ". escapeshellarg($strTargetFile)." ". escapeshellarg($strTargetFile));

    } else {
        echo "Skipping ".$strSourceFile.", not existing".PHP_EOL;
    }
}
