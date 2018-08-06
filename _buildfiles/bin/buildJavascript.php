#!/usr/bin/php
<?php

echo "merge and install js dependencies".PHP_EOL;

$strRoot = realpath(__DIR__."/../../..");

$arrIncludedModules = [];
if (is_file($strRoot."/project/packageconfig.php")) {
    include $strRoot."/project/packageconfig.php";
}
if (!isset($arrExcludedModules["core"])) {
    $arrExcludedModules["core"] = [];
}

$objCoreDirs = new DirectoryIterator($strRoot);
$jsFiles = [];

foreach ($objCoreDirs as $objCoreDir) {
    if ($objCoreDir->isDir() && substr($objCoreDir->getFilename(), 0, 4) == 'core') {
        $objModuleDirs = new DirectoryIterator($objCoreDir->getRealPath());
        foreach ($objModuleDirs as $objDir) {

            //defined as included?
            if (isset($arrIncludedModules[$objCoreDir->getFilename()]) && !in_array($objDir->getFilename(), $arrIncludedModules[$objCoreDir->getFilename()])) {
                continue;
            }

            //defined as excluded?
            if (isset($arrExcludedModules[$objCoreDir->getFilename()]) && in_array($objDir->getFilename(), $arrExcludedModules[$objCoreDir->getFilename()])) {
                continue;
            }

            $providesJson = $objDir->getRealPath() . "/scripts/provides.json";
            if (is_file($providesJson)) {
                $provides = json_decode(file_get_contents($providesJson), true);
                if (isset($provides["paths"])) {
                    foreach ($provides["paths"] as $name => $jsFile) {
                        $path = realpath($objDir->getRealPath() . "/scripts/{$jsFile}.js");
                        if (!empty($path)) {
                            if (!in_array($path, $jsFiles)) {
                                $jsFiles[$name] = $path;
                            }
                        } else {
                            throw new \RuntimeException("provides.json contains an invalid javascript file reference: {$jsFile}");
                        }
                    }
                }
            }
        }
    }
}

echo "merge all js files\n";

$content = "";

// add global js libs
$globals = [
    __DIR__ . "/../../module_system/scripts/jquery/jquery.min.js",
    __DIR__ . "/../../module_system/scripts/routie/routie.min.js",
    __DIR__ . "/../../module_system/scripts/requirejs/require.js",
];

foreach ($globals as $file) {
    $js = file_get_contents($file);

    $content.= "\n/* -- {$file} */\n\n";
    $content.= $js;
}

// requirejs libs
foreach ($jsFiles as $name => $file) {

    if (pathinfo($file, PATHINFO_EXTENSION) == "js") {
        $js = file_get_contents($file);

        // in case the js has no define make a wrapper this shoudl be roughly the same behaviour as the requirejs loader
        if (strpos($js, "define(") === false) {
            $js = 'define("' . $name . '", [], function(){ ' . "\n" . $js . "\n" . ' });';
        }

        $content.= "\n/* -- {$file} */\n\n";
        $content.= $js;
    }
}

echo "found " . count($jsFiles) . " js files\n";
file_put_contents('plain.js', $content);

// minify
if (is_file("plain.js")) {
    echo "minfiy merged js files\n";
    $strUglifyjsBin = "node " . __DIR__ . "/../jstests/node_modules/uglify-js/bin/uglifyjs";
    system($strUglifyjsBin . " plain.js -o plain.min.js");
}

// build type script
echo "compile type script files\n";
$strTscBin = "node " . __DIR__ . "/../jstests/node_modules/typescript/bin/tsc";
system($strTscBin . " --build tsconfig.json");

// minify ts
if (is_file("tsc.js")) {
    echo "minfy type script file\n";
    $strUglifyjsBin = "node " . __DIR__ . "/../jstests/node_modules/uglify-js/bin/uglifyjs";
    system($strUglifyjsBin . " tsc.js -o tsc.min.js");
}

// merge type script and js files
echo "Build agp js\n";
$plain = is_file("plain.min.js") ? file_get_contents("plain.min.js") : "";
$tsc = is_file("tsc.min.js") ? file_get_contents("tsc.min.js") : "";

file_put_contents($strRoot . "/core/module_system/scripts/agp.min.js", $plain . "\n\n" . $tsc);

