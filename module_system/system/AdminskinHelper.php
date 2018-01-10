<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                            *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\Admin\AdminskinImageresolverInterface;
use Kajona\V4skin\Admin\Skins\Kajona_V4\AdminskinImageresolver;


/**
 * A small class providing a few methods handling the admin-skins available
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.0
 */
class AdminskinHelper
{

    private static $strSkinPath = null;

    /**
     * @var AdminskinImageresolverInterface
     */
    private static $objAdminImageResolver = null;

    /**
     * Returns the list of available admin-skins
     *
     * @static
     * @return array
     */
    public static function getListOfAdminskinsAvailable()
    {

        $arrFiles = Resourceloader::getInstance()->getFolderContent("/admin/skins", array(), true);

        $arrReturn = array();
        foreach ($arrFiles as $strPath => $strName) {
            if (is_dir($strPath)) {
                $arrReturn[$strPath] = $strName;
            }
        }

        return $arrReturn;

    }

    /**
     * Loads the file-system path for a single skin
     *
     * @static
     *
     * @internal string $strSkin
     *
     * @return string
     * @deprecated
     */
    public static function getPathForSkin($strSkin = "")
    {
        if (self::$strSkinPath == null) {
            self::$strSkinPath = Resourceloader::getInstance()->getPathForFolder("/admin/skins/kajona_v4");
        }

        return self::$strSkinPath;
    }

    /**
     * Internal helper, sets the current skinwebpath to be used by skins
     *
     * @static
     * @return void
     */
    public static function defineSkinWebpath()
    {
        if (!defined("_skinwebpath_")) {
            define("_skinwebpath_", _webpath_.self::getPathForSkin());
        }
    }

    /**
     * Makes use of the admin-skin image-mapper to resolve an image-name into a
     * real image-tag / script / whatever
     *
     * @param string $strName
     * @param string $strAlt
     * @param bool $bitBlockTooltip
     * @param string $strEntryId
     *
     * @return string
     */
    public static function getAdminImage($strName, $strAlt = "", $bitBlockTooltip = false, $strEntryId = "")
    {

        if (is_array($strName) && count($strName) == 2) {
            $strAlt = $strName[1];
            $strName = $strName[0];
        }

        if (self::$objAdminImageResolver == null) {
            self::$objAdminImageResolver = new AdminskinImageresolver();
        }

        return self::$objAdminImageResolver->getImage($strName, $strAlt, $bitBlockTooltip, $strEntryId);
    }
}

