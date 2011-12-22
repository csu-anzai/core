<?php
/*"******************************************************************************************************
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_admin.php 4363 2011-12-12 15:34:56Z sidler $	                                            *
********************************************************************************************************/


/**
 * Class holding common methods for extended and simplified admin-guis.
 *
 * @module module_system
 * @since 4.0
 */
abstract class class_admin_simple extends class_admin {

    private $strPeAddon = "";

    public function __construct($strSystemid = "") {
        parent::__construct($strSystemid);

        if($this->getParam("pe") == "1")
            $this->strPeAddon = "&pe=1";
    }


    /**
     * Renders the form to create a new entry
     * @abstract
     * @return string
     * @permissions edit
     */
    protected abstract function actionNew();

    /**
     * Renders the form to edit an existing entry
     * @abstract
     * @return string
     * @permissions edit
     */
    protected abstract function actionEdit();

    /**
     * Renders the general list of records
     * @abstract
     * @return string
     * @permissions view
     */
    protected abstract function actionList();


    /**
     * A general action to delete a record.
     * This method may be overwritten by subclasses.
     *
     * @permissions delete
     */
    protected function actionDelete() {
        $objRecord = class_objectfactory::getInstance()->getObject($this->getSystemid());
        if($objRecord != null && $objRecord->rightDelete()) {
            if(!$objRecord->deleteObject())
                throw new class_exception("error deleting object ".$objRecord->getStrDisplayName(), class_exception::$level_ERROR);

            $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "list"));
        }
        else
            throw new class_exception("error loading object ".$this->getSystemid(), class_exception::$level_ERROR);
    }

    /**
     * Renders a list of items, target is the common admin-list.
     * Please be aware, that the combination of paging and sortable-lists may result in unpredictable ordering.
     * As soon as the list is sortable, the page-size should be at least the same as the number of elements
     *
     * @param class_array_section_iterator $objArraySectionIterator
     * @param bool $bitSortable
     * @param $strListIdentifier an internal identifier to check the current parent-list
     * @return string
     */
    protected function renderList(class_array_section_iterator $objArraySectionIterator, $bitSortable = false, $strListIdentifier = "") {
        $strReturn = "";
        $intI = 0;

        if($bitSortable && $objArraySectionIterator->getNrOfPages() > 1) {
            throw new class_exception("sortable lists with more than one page are not supported!", class_exception::$level_ERROR);
        }

        $strListId = generateSystemid();

        $arrPageViews = $this->objToolkit->getSimplePageview($objArraySectionIterator, $this->getArrModule("modul"), $this->getAction(), "&systemid=".$this->getSystemid().$this->strPeAddon);
        $arrIterables = $arrPageViews["elements"];

        if(count($arrIterables) == 0)
            $strReturn .= $this->objToolkit->getTextRow($this->getText("commons_list_empty"));

        if($bitSortable)
            $strReturn .= $this->objToolkit->dragableListHeader($strListId);
        else
            $strReturn .= $this->objToolkit->listHeader();

        if($this->renderLevelUpAction($strListIdentifier) != "") {
            $strReturn .= $this->objToolkit->genericAdminList("", "", "", $this->objToolkit->listButton($this->renderLevelUpAction($strListIdentifier)), $intI++);
        }

        if(count($arrIterables) > 0) {

            /** @var $objOneIterable class_model|interface_model|interface_admin_listable */
            foreach($arrIterables as $objOneIterable) {

                if(!$objOneIterable->rightView())
                    continue;

                $strActions = "";
                $strActions .= $this->renderEditAction($objOneIterable);

                foreach($this->renderAdditionalActions($objOneIterable) as $strOneEntry) {
                    $strActions .= $strOneEntry;
                }

                $strActions .= $this->renderDeleteAction($objOneIterable);
                $strActions .= $this->renderStatusAction($objOneIterable);
                $strActions .= $this->renderPermissionsAction($objOneIterable);

                //$strReturn .= $this->objToolkit->listRow2Image(getImageAdmin($objOneIterable->getStrIcon()), $objOneIterable->getStrDisplayName(), $strActions, $intI++);
                $strReturn .= $this->objToolkit->simpleAdminList($objOneIterable, $strActions, $intI++);
            }


        }

        if($this->getNewEntryAction($strListIdentifier) != "") {
            $strReturn .= $this->objToolkit->genericAdminList("", "", "", $this->objToolkit->listButton($this->getNewEntryAction($strListIdentifier)), $intI);
        }

        if($bitSortable)
            $strReturn .= $this->objToolkit->dragableListFooter($strListId);
        else
            $strReturn .= $this->objToolkit->listFooter();

        $strReturn .= $arrPageViews["pageview"];

        return $strReturn;
    }

    /**
     * Renders the action to jump a level upwards.
     * Overwrite this method if you want to provide such an action.
     *
     * @param $strListIdentifier
     * @return string
     */
    protected function renderLevelUpAction($strListIdentifier) {
        return "";
    }

    /**
     * Renders the edit action button for the current record.
     * @param class_model $objListEntry
     * @return string
     */
    protected function renderEditAction(class_model $objListEntry) {
        if($objListEntry->rightEdit()) {
            return $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "edit", "&systemid=".$objListEntry->getSystemid().$this->strPeAddon, $this->getText("commons_list_edit"), $this->getText("commons_list_edit"), "icon_pencil.gif"));
        }
    }

    /**
     * Renders the delete action button for the current record.
     * @param \class_model|\interface_model $objListEntry
     * @return string
     */
    protected function renderDeleteAction(interface_model $objListEntry) {
        if($objListEntry->rightDelete()) {
            return $this->objToolkit->listDeleteButton($objListEntry->getStrDisplayName(), $this->getText("delete_question"), getLinkAdminHref($this->getArrModule("modul"), "delete", "&systemid=".$objListEntry->getSystemid().$this->strPeAddon));
        }
    }

    /**
     * Renders the status action button for the current record.
     * @param class_model $objListEntry
     * @return string
     */
    protected function renderStatusAction(class_model $objListEntry) {
        if($objListEntry->rightEdit() && $this->strPeAddon == "") {
            return $this->objToolkit->listStatusButton($objListEntry->getSystemid(), false);
        }
    }

    /**
     * Renders the permissions action button for the current record.
     * @param class_model $objListEntry
     * @return string
     */
    protected function renderPermissionsAction(class_model $objListEntry) {
        if($objListEntry->rightRight() && $this->strPeAddon == "") {
            return $this->objToolkit->listButton(getLinkAdmin("right", "change", "&systemid=".$objListEntry->getSystemid().$this->strPeAddon, "", $this->getText("commons_edit_permissions"), getRightsImageAdminName($objListEntry->getSystemid())));
        }
    }

    /**
     * Returns an additional set of action-buttons rendered right after the edit-action.
     *
     * @param class_model $objListEntry
     * @return array
     */
    protected function renderAdditionalActions(class_model $objListEntry) {
        return array();
    }

    /**
     * Renders the action to add a new record to the end of the list.
     * Make sure you have the lang-key "module_action_new" in the modules' lang-file.
     * @param $strListIdentifier an internal identifier to check the current parent-list
     * @return string
     */
    protected function getNewEntryAction($strListIdentifier) {
        if($this->getObjModule()->rightEdit()) {
            return getLinkAdmin($this->getArrModule("modul"), "new", $this->strPeAddon, $this->getText("module_action_new"), $this->getText("module_action_new"), "icon_new.gif");
        }
    }


}

