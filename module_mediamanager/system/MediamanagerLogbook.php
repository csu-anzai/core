<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System;

use Kajona\System\System\Carrier;


/**
 * Model for the downloads-logbook
 *
 * @package module_mediamanager
 * @author sidler@mulchprod.de
 *
 * @module mediamanager
 * @moduleId _mediamanager_module_id_
 */
class MediamanagerLogbook extends \Kajona\System\System\Model implements \Kajona\System\System\ModelInterface
{

    /**
     * Generates an entry in the logbook an increases the hits-counter
     *
     * @param MediamanagerFile $objFile
     */
    public static function generateDlLog(MediamanagerFile $objFile)
    {
        $objDB = Carrier::getInstance()->getObjDB();
        $strQuery = "INSERT INTO agp_mediamanager_dllog
	                   (downloads_log_id, downloads_log_date, downloads_log_file, downloads_log_user, downloads_log_ip, downloads_log_file_id) VALUES
	                   (?, ?, ?, ?, ?, ?)";

        $objDB->_pQuery($strQuery, array(generateSystemid(), (int)time(), basename($objFile->getStrFilename()),
            Carrier::getInstance()->getObjSession()->getUserID(), getServer("REMOTE_ADDR"), $objFile->getSystemid()));

        $objFile->increaseHits();
    }

    /**
     * Loads the records of the dl-logbook
     *
     * @static
     *
     * @param null $intStart
     * @param null $intEnd
     *
     * @return mixed AS ARRAY
     */
    public static function getLogbookData($intStart = null, $intEnd = null)
    {
        $strQuery = "SELECT *
					  FROM agp_mediamanager_dllog
					  ORDER BY downloads_log_date DESC";
        return Carrier::getInstance()->getObjDB()->getPArray($strQuery, array(), $intStart, $intEnd);

    }

    /**
     * Counts the number of logs available
     *
     * @return int
     */
    public function getLogbookDataCount()
    {
        $strQuery = "SELECT COUNT(*) AS cnt
					  FROM agp_mediamanager_dllog";
        $arrTemp = $this->objDB->getPRow($strQuery, array());
        return $arrTemp["cnt"];

    }


    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return "";
    }
}

