<?php
/*"******************************************************************************************************
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_modul_user_log.php 3530 2011-01-06 12:30:26Z sidler $                                    *
********************************************************************************************************/

/**
 * The changelog is a global wrapper to the gui-based logging.
 * Changes should reflect user-changes and not internal system-logs.
 * For logging to the logfile, see class_logger.
 * But: entries added to the changelog are copied to the systemlog leveled as information, too.
 * Changes are stored as a flat list in the database only and have no representation within the
 * system-table. This means there are no common system-id relations.
 *
 * @package modul_system
 * @author sidler@mulchprod.de
 * @see class_logger
 */
class class_modul_system_changelog extends class_model implements interface_model  {

    /**
     * Constructor to create a valid object
     *
     * @param string $strSystemid (use "" on new objects)
     */
    public function __construct($strSystemid = "") {
        $arrModul = array();
        $arrModul["name"] 				= "modul_system";
		$arrModul["moduleId"] 			= _system_modul_id_;
		$arrModul["table"]       		= _dbprefix_."changelog";
		$arrModul["modul"]				= "system";

		//base class
		parent::__construct($arrModul, $strSystemid);

		//init current object
		if($strSystemid != "")
		    $this->initObject();
    }

    /**
     * @see class_model::getObjectTables();
     * @return array
     */
    protected function getObjectTables() {
        return array();
    }

    /**
     * @see class_model::getObjectDescription();
     * @return string
     */
    protected function getObjectDescription() {
        return "system changelog";
    }

    /**
     * Initalises the current object, if a systemid was given
     */
    public function initObject() {
    }

    /**
     * Generates a new entry in the modification log storing all relevant information.
     * Creates an entry in the systemlog leveled as information, too.
     * By default entries with same old- and new-values are dropped.
     *
     * @param string $strModule
     * @param string $strAction
     * @param string $strSystemid
     * @param string $strProperty
     * @param string $strOldvalue
     * @param string $strNewvalue
     * @param bool $bitForceEntry if set to true, an entry will be created even if the values didn't change
     * @return bool
     */
    public function createLogEntry($strModule, $strAction, $strSystemid, $strProperty, $strOldvalue, $strNewvalue, $bitForceEntry = false) {

        if(!$bitForceEntry && ($strOldvalue == $strNewvalue) )
            return true;

        class_logger::getInstance()->addLogRow("change in module ".$strModule."@".$strAction." systemid: ".$strSystemid." old value: ".uniStrTrim($strOldvalue, 60)." new value: ".uniStrTrim($strNewvalue, 60), class_logger::$levelInfo);

        $strQuery = "INSERT INTO ".$this->arrModule["table"]."
                     (change_id,
                      change_date,
                      change_systemid,
                      change_user,
                      change_module,
                      change_action,
                      change_property,
                      change_oldvalue,
                      change_newvalue) VALUES
                     ('".dbsafeString(generateSystemid())."',
                       ".dbsafeString(class_date::getCurrentTimestamp()).",
                      '".dbsafeString($strSystemid)."',
                      '".dbsafeString($this->objSession->getUserID())."',
                      '".dbsafeString($strModule)."',
                      '".dbsafeString($strAction)."',
                      '".dbsafeString($strProperty)."',
                      '".dbsafeString($strOldvalue)."',
                      '".dbsafeString($strNewvalue)."')";

        return $this->objDB->_query($strQuery);
    }

    /**
     * Creates the list of logentries, either without a systemid-based filter
     * or limited to the given systemid.
     *
     * @param string $strSystemidFilter
     * @param int $intStartDate
     * @param int $intEndDate
     * @return class_changelog_container
     */
    public static function getLogEntries($strSystemidFilter = "", $intStart = null, $intEnd = null) {
        $strQuery = "SELECT *
                       FROM "._dbprefix_."changelog
                      ".($strSystemidFilter != "" ? " WHERE change_systemid = '".  dbsafeString($strSystemidFilter)."' ": "")."
                   ORDER BY change_date DESC";

        if($intStart != null && $intEnd != null)
            $arrRows = class_carrier::getInstance()->getObjDB()->getArraySection($strQuery, $intStart, $intEnd);
        else
            $arrRows = class_carrier::getInstance()->getObjDB()->getArray($strQuery);

        $arrReturn = array();
        foreach($arrRows as $arrRow)
            $arrReturn[] = new class_changelog_container($arrRow["change_date"], $arrRow["change_systemid"], $arrRow["change_user"],
                           $arrRow["change_module"], $arrRow["change_action"], $arrRow["change_property"], $arrRow["change_oldvalue"], $arrRow["change_newvalue"]);

        return $arrReturn;
    }

    /**
     * Counts the number of logentries available
     *
     * @param string $strSystemidFilter
     * @return int
     */
    public static function getLogEntriesCount($strSystemidFilter = "") {
        $strQuery = "SELECT COUNT(*)
                       FROM "._dbprefix_."changelog
                      ".($strSystemidFilter != "" ? " WHERE change_systemid = '".  dbsafeString($strSystemidFilter)."' ": "")."
                   ORDER BY change_date DESC";

        $arrRow = class_carrier::getInstance()->getObjDB()->getRow($strQuery);
        return $arrRow["COUNT(*)"];
    }
   
}

/**
 * Simple data-container for logentries.
 * Has no regular use.
 */
final class class_changelog_container {
    private $objDate;
    private $strSystemid;
    private $strUserId;
    private $strModule;
    private $strAction;
    private $strProperty;
    private $strOldValue;
    private $strNewValue;

    function __construct($intDate, $strSystemid, $strUserId, $strModule, $strAction, $strProperty, $strOldValue, $strNewValue) {
        $this->objDate = new class_date($intDate);
        $this->strSystemid = $strSystemid;
        $this->strUserId = $strUserId;
        $this->strModule = $strModule;
        $this->strAction = $strAction;
        $this->strProperty = $strProperty;
        $this->strOldValue = $strOldValue;
        $this->strNewValue = $strNewValue;
    }

    public function getObjDate() {
        return $this->objDate;
    }

    public function setObjDate($objDate) {
        $this->objDate = $objDate;
    }

    public function getStrSystemid() {
        return $this->strSystemid;
    }

    public function setStrSystemid($strSystemid) {
        $this->strSystemid = $strSystemid;
    }

    public function getStrUserId() {
        return $this->strUserId;
    }

    public function getStrUsername() {
        $objUser = new class_modul_user_user($this->getStrUserId());
        return $objUser->getStrUsername();
    }

    public function setStrUserId($strUserId) {
        $this->strUserId = $strUserId;
    }

    public function getStrModule() {
        return $this->strModule;
    }

    public function setStrModule($strModule) {
        $this->strModule = $strModule;
    }

    public function getStrAction() {
        return $this->strAction;
    }

    public function setStrAction($strAction) {
        $this->strAction = $strAction;
    }

    public function getStrOldValue() {
        return $this->strOldValue;
    }

    public function setStrOldValue($strOldValue) {
        $this->strOldValue = $strOldValue;
    }

    public function getStrNewValue() {
        return $this->strNewValue;
    }

    public function setStrNewValue($strNewValue) {
        $this->strNewValue = $strNewValue;
    }

    public function getStrProperty() {
        return $this->strProperty;
    }

    public function setStrProperty($strProperty) {
        $this->strProperty = $strProperty;
    }






}
?>