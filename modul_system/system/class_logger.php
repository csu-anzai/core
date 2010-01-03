<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2010 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

/**
 * The class_logger provides a small and fast logging-engine to generate a debug logfile.
 * The granularity of the logging is defined in the config.php
 *
 * @package modul_system
 */
final class class_logger {

    /**
     * Level to be used for real errors
     *
     * @var int
     * @static
     */
    public static $levelError = 0;

    /**
     * Level to be used for warnings
     *
     * @var int
     * @static
     */
    public static $levelWarning = 1;

    /**
     * Level to be used for infos
     *
     * @var int
     * @static
     */
    public static $levelInfo = 2;

    /**
     * Instance of the logger
     *
     * @var class_logger
     */
    private static $objInstance = null;

    /**
     * Constant defining the filename
     *
     * @var string
     */
    private $strFilename = "systemlog.log";

    private $intLogLevel = 0;

    /**
     * Doing nothing but being private
     *
     */
    private function __construct() {
        $this->intLogLevel = class_carrier::getInstance()->getObjConfig()->getDebug("debuglogging");
    }

    /**
     * returns the current instance of this class
     *
     * @return class_logger
     */
    public static function getInstance() {
        if (class_logger::$objInstance == null)
            class_logger::$objInstance = new class_logger();

        return self::$objInstance;
    }

    /**
     * Adds a row to the current log
     * For $intLevel use on of the static level provided by this class
     *
     * @param string $strMessage
     * @param int $intLevel
     */
    public function addLogRow($strMessage, $intLevel) {

        //check, if there someting to write
        if($this->intLogLevel == 0)
            return;
        //errors in level >=1
        if($intLevel == self::$levelError && $this->intLogLevel < 1)
            return;
        //warnings in level >=2
        if($intLevel == self::$levelWarning && $this->intLogLevel < 2)
            return;
        //infos in level >=3
        if($intLevel == self::$levelInfo && $this->intLogLevel < 3)
            return;

        //a log row has the following scheme:
        // YYYY-MM-DD HH:MM LEVEL USERID (USERNAME) MESSAGE
        $strDate = strftime("%Y-%m-%d %H:%M", time());
        $strLevel = "";
        if($intLevel == self::$levelError)
            $strLevel = "ERROR";
        elseif ($intLevel == self::$levelInfo)
            $strLevel = "INFO";
        elseif ($intLevel == self::$levelWarning)
            $strLevel = "WARNING";

        $strSessid = class_carrier::getInstance()->getObjSession()->getInternalSessionId();
        $strSessid .= " (".class_carrier::getInstance()->getObjSession()->getUsername().")";

        $strText = $strDate." ".$strLevel." ".$strSessid." ".$strMessage."\r\n";

		$handle = fopen(_systempath_."/debug/".$this->strFilename, "a");
		fwrite($handle, $strText);
		fclose($handle);
    }

    /**
     * Returns the complete log-file as one string
     *
     * @return string
     */
    public function getLogFileContent() {
        return @file_get_contents(_systempath_."/debug/".$this->strFilename);
    }
}


?>