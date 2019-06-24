<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\System\System\AdminskinHelper;

$config["file_marks"] = [
    0 => MediamanagerFile::getDefaultFileMarker(),
    1 => AdminskinHelper::getAdminImage("icon_flag_blue"),
    2 => AdminskinHelper::getAdminImage("icon_flag_brown"),
    3 => AdminskinHelper::getAdminImage("icon_flag_green"),
    4 => AdminskinHelper::getAdminImage("icon_flag_orange"),
    5 => AdminskinHelper::getAdminImage("icon_flag_red"),
    6 => AdminskinHelper::getAdminImage("icon_dot")
];