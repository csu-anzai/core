#!/usr/bin/php
<?php

echo "merge and install vendor dependencies".PHP_EOL;

$strRoot = realpath(__DIR__."/../../..");

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

// merge composer files
$vendorComposer = $strRoot."/core/module_vendor/composer.json";

$composer = json_decode(file_get_contents($vendorComposer), true);
$composer["require"] = [];

$objCoreDirs = new DirectoryIterator($strRoot);
foreach ($objCoreDirs as $objCoreDir) {
    if ($objCoreDir->isDir() && substr($objCoreDir->getFilename(), 0, 4) == 'core') {
        $objModuleDirs = new DirectoryIterator($objCoreDir->getRealPath());
        foreach ($objModuleDirs as $objDir) {
            if ($objDir->getFilename() == 'module_vendor') {
                continue;
            }

            if (substr($objDir->getFilename(), 0, 7) == 'module_') {
                // exclude
                if (isset($arrExcludedModules[$objCoreDir->getFilename()]) && in_array($objDir->getFilename(), $arrExcludedModules[$objCoreDir->getFilename()])) {
                    continue;
                }

                $composerFile = $objDir->getRealPath() . '/composer.json';
                if (is_file($composerFile)) {
                    $content = json_decode(file_get_contents($composerFile), true);

                    if (isset($content["require"]) && is_array($content["require"])) {
                        foreach ($content["require"] as $name => $version) {
                            if (strpos($name, "/") !== false && isset($composer["require"][$name])) {
                                if ($composer["require"][$name] != $version) {
                                    throw new \RuntimeException("Found dependency {$name} multiple times with different version {$composer["require"][$name]} vs {$version}");
                                }
                            }

                            $composer["require"][$name] = $version;
                        }
                    }
                }
            }
        }
    }
}

file_put_contents($vendorComposer, json_encode($composer, JSON_PRETTY_PRINT));

// install composer
$arrOutput = array();
$intReturn = 0;
exec('composer install --prefer-dist --working-dir  ' . escapeshellarg(dirname($vendorComposer)), $arrOutput, $intReturn);
if ($intReturn == 127) {
    echo "<span style='color: red;'>composer was not found. please run 'composer install --prefer-dist --working-dir " . dirname($vendorComposer) . "' manually</span>\n";
}
if ($intReturn == 1) {
    echo "<span style='color: red;'>composer error. please run 'composer install --prefer-dist --working-dir " . dirname($vendorComposer) . "' manually</span>\n";

    if (!is_writable(dirname($vendorComposer))) {
        echo "<span style='color: red;'>    target folder " . dirname($vendorComposer) . " is not writable</span>\n";
    }
}
echo "Composer install finished for " . $vendorComposer . ": \n";

echo "   " . implode("\n   ", $arrOutput);
