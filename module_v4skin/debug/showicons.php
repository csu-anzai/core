<?php

echo "<link href=\"" . _webpath_ . "/core_agp/module_agpskin/admin/skins/agp/less/bootstrap.less?0\" rel=\"stylesheet/less\">\n";
echo "<script> less = { env:'production' }; </script>\n";
echo "<script src=\"" . _webpath_ . "/core/module_v4skin/admin/skins/kajona_v4/less/less.min.js\"></script>\n";

define("_skinwebpath_", "/");

$arrIcons = \Kajona\V4skin\Admin\Skins\Kajona_V4\AdminskinImageresolver::$arrFAImages;
$objResolver = new \Kajona\V4skin\Admin\Skins\Kajona_V4\AdminskinImageresolver();

foreach ($arrIcons as $strName => $strImage) {
    echo $strName . " => " . $objResolver->getImage($strName) . "\n";
}

