<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

/**
 * Business object for a single event. Holds all values to control the event
 *
 * @package module_eventmanager
 * @author sidler@mulchprod.de
 * @since 3.4
 *
 * @targetTable em_event.em_ev_id
 *
 * @module eventmanager
 * @moduleId _eventmanager_module_id_
 */
class class_module_eventmanager_event extends \Kajona\System\System\Model implements \Kajona\System\System\ModelInterface, interface_versionable, interface_admin_listable  {

    /**
     * @var string
     * @tableColumn em_event.em_ev_title
     * @tableColumnDatatype char254
     * @versionable
     * @addSearchIndex
     *
     * @fieldType text
     * @fieldMandatory
     * @fieldLabel commons_title
     *
     * @addSearchIndex
     * @templateExport
     */
    private $strTitle = "";

    /**
     * @var string
     * @tableColumn em_event.em_ev_description
     * @tableColumnDatatype text
     * @versionable
     * @blockEscaping
     * @addSearchIndex
     *
     * @fieldType wysiwygsmall
     * @fieldLabel commons_description
     *
     * @addSearchIndex
     * @templateExport
     */
    private $strDescription = "";

    /**
     * @var string
     * @tableColumn em_event.em_ev_location
     * @tableColumnDatatype char254
     * @versionable
     * @addSearchIndex
     *
     * @fieldType textarea
     * @fieldLabel event_location
     *
     * @addSearchIndex
     * @templateExport
     */
    private $strLocation = "";

    /**
     * @var int
     * @tableColumn em_event.em_ev_eventstatus
     * @tableColumnDatatype int
     * @versionable
     *
     * @fieldType dropdown
     * @fieldDDValues [1 => event_status_1],[2 => event_status_2],[3 => event_status_3],[4 => event_status_4]
     * @templateExport
     */
    private $intEventStatus;

    /**
     * @var int
     * @tableColumn em_event.em_ev_participant_registration
     * @tableColumnDatatype int
     * @versionable
     *
     * @fieldType yesno
     * @fieldMandatory
     * @fieldLabel event_registration
     * @templateExport
     */
    private $intRegistrationRequired = 0;

    /**
     * @var int
     * @tableColumn em_event.em_ev_participant_limit
     * @tableColumnDatatype int
     * @versionable
     *
     * @fieldType yesno
     * @fieldLabel event_limitparticipants
     * @templateExport
     */
    private $intLimitGiven = 0;

    /**
     * @var int
     * @tableColumn em_event.em_ev_participant_max
     * @tableColumnDatatype int
     * @versionable
     *
     * @fieldType text
     * @fieldValidator class_posint_validator
     * @fieldLabel event_maxparticipants
     * @templateExport
     */
    private $intParticipantsLimit = 0;

    /**
     * For form generation only
     * @var \Kajona\System\System\Date
     * @versionable
     * @fieldType datetime
     * @fieldLabel event_start
     * @fieldMandatory
     * @templateExport
     * @templateMapper datetime
     */
    private $objStartDate;

    /**
     * For form-generation only
     * @var \Kajona\System\System\Date
     * @versionable
     * @fieldType datetime
     * @fieldLabel event_end
     * @templateExport
     * @templateMapper datetime
     */
    private $objEndDate;



    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin(). Alternatively, you may return an array containing
     *         [the image name, the alt-title]
     */
    public function getStrIcon() {
        return "icon_event";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo() {
        $strCenter = "(".dateToString($this->getObjStartDate());
        if($this->getObjEndDate() != null)
            $strCenter .= " - ".dateToString($this->getObjEndDate());

        if($this->getIntRegistrationRequired()) {
            $strCenter .= ", ". class_module_eventmanager_participant::getObjectCount($this->getSystemid())." ".$this->getLang("event_participant");
        }

        $strCenter .= ")";
        return $strCenter;
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription() {
        return "";
    }

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName() {
        return $this->getStrTitle();
    }

    public function isParticipant($strUserid) {
        return class_module_eventmanager_participant::getParticipantByUserid($strUserid, $this->getSystemid()) !== null;
    }


    /**
     * Returns a list of events available
     *
     * @param bool|int $intStart
     * @param bool|int $intEnd
     * @param \Kajona\System\System\Date $objStartDate
     * @param \Kajona\System\System\Date $objEndDate
     * @param bool $bitOnlyActive
     * @param int $intOrder
     * @param null $intStatusFilter
     *
     * @return class_module_eventmanager_event[]
     */
    public static function getAllEvents($intStart = false, $intEnd = false, \Kajona\System\System\Date $objStartDate = null, \Kajona\System\System\Date $objEndDate = null, $bitOnlyActive = false, $intOrder = 0, $intStatusFilter = null) {


        $objORM = new class_orm_objectlist();

        if($objStartDate != null && $objEndDate != null) {
            $objORM->addWhereRestriction(new class_orm_objectlist_restriction("AND (system_date_start > ? AND system_date_start <= ?) ", array($objStartDate->getLongTimestamp(), $objEndDate->getLongTimestamp())));

        }
        if($intStatusFilter != null) {
            $objORM->addWhereRestriction(new class_orm_objectlist_restriction("AND em_ev_eventstatus = ?", array($intStatusFilter)));
        }

        if($bitOnlyActive) {
            $objORM->addWhereRestriction(new class_orm_objectlist_systemstatus_restriction(class_orm_comparator_enum::Equal(), 1));
        }

        $objORM->addOrderBy(new class_orm_objectlist_orderby("system_date_start ".($intOrder == "1" ? " ASC " : " DESC ")));
        $objORM->addOrderBy(new class_orm_objectlist_orderby("em_ev_title  ASC "));
        return $objORM->getObjectList(get_called_class(), "", $intStart, $intEnd);
    }

    /**
     * Returns a human readable name of the action stored with the changeset.
     *
     * @param string $strAction the technical actionname
     *
     * @return string the human readable name
     */
    public function getVersionActionName($strAction) {
        if($strAction == class_module_system_changelog::$STR_ACTION_EDIT)
            return $this->getLang("event_edit");

        return $strAction;
    }

    /**
     * Returns a human readable name of the record / object stored with the changeset.
     *
     * @return string the human readable name
     */
    public function getVersionRecordName() {
        return $this->getLang("change_object_participant");
    }

    /**
     * Returns a human readable name of the property-name stored with the changeset.
     *
     * @param string $strProperty the technical property-name
     *
     * @return string the human readable name
     */
    public function getVersionPropertyName($strProperty) {
        return $strProperty;
    }

    /**
     * Renders a stored value. Allows the class to modify the value to display, e.g. to
     * replace a timestamp by a readable string.
     *
     * @param string $strProperty
     * @param string $strValue
     *
     * @return string
     */
    public function renderVersionValue($strProperty, $strValue) {
        if( ($strProperty == "objEndDate" || $strProperty == "objStartDate") && $strValue != "") {
            return dateToString(new \Kajona\System\System\Date($strValue));
        }
        if($strProperty == "limitGiven" || $strProperty == "registrationRequired") {
            return $this->getLang("event_yesno_".$strValue, "eventmanager");
        }
        return $strValue;
    }




    public function getStrTitle() {
        return $this->strTitle;
    }

    public function setStrTitle($strTitle) {
        $this->strTitle = $strTitle;
    }

    public function getStrDescription() {
        return $this->strDescription;
    }

    public function setStrDescription($strDescription) {
        $this->strDescription = $strDescription;
    }

    public function getStrLocation() {
        return $this->strLocation;
    }

    public function setStrLocation($strLocation) {
        $this->strLocation = $strLocation;
    }

    public function getIntRegistrationRequired() {
        return $this->intRegistrationRequired;
    }

    public function setIntRegistrationRequired($intRegistration) {
        $this->intRegistrationRequired = $intRegistration;
    }

    public function getIntLimitGiven() {
        return $this->intLimitGiven;
    }

    public function setIntLimitGiven($intLimitGiven) {
        $this->intLimitGiven = $intLimitGiven;
    }

    public function getIntParticipantsLimit() {
        return $this->intParticipantsLimit;
    }

    public function setIntParticipantsLimit($intParticipantsLimit) {
        $this->intParticipantsLimit = (int)$intParticipantsLimit;
    }

    public function setIntEventStatus($intEventStatus) {
        $this->intEventStatus = $intEventStatus;
    }

    public function getIntEventStatus() {
        return $this->intEventStatus;
    }


}
