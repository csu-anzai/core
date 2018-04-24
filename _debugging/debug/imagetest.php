<?php
namespace Kajona\Debugging\Debug;

use Artemeon\Image\Image;
use Artemeon\Image\Plugins\ImageRotate;
use Artemeon\Image\Plugins\ImageScaleAndCrop;

$floatAngle = 90.0;

$objImage = new Image(_images_cachepath_);
$objImage->setUseCache(false);
if (!$objImage->load(_realpath_."/files/images/samples/P9066809.JPG")) {
    echo "Could not load file.\n";
}

$objImage->addOperation(new ImageRotate($floatAngle, "#ffffffff"));
$objImage->addOperation(new ImageScaleAndCrop(800, 1350));

if (!$objImage->save(_realpath_."/files/cache/P9066809_transformed.PNG", Image::FORMAT_PNG)) {
    echo "File not saved.\n";
}