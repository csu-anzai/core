<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                            *
********************************************************************************************************/


/**
 * Admin-Parts of the filemanager
 *
 * @package module_filemanager
 * @author sidler@mulchprod.de
 */
class class_module_filemanager_admin extends class_admin_simple implements  interface_admin {
	private $strFolder;
	private $strFolderOld;

	/**
	 * Constructor
	 */
	public function __construct() {
        $this->setArrModuleEntry("modul", "filemanager");
        $this->setArrModuleEntry("moduleId", _filemanager_modul_id_);
		parent::__construct();


		$this->getCurrentFolder();
	}

	protected function getOutputModuleNavi() {
	    $arrReturn = array();
        $arrReturn[] = array("right", getLinkAdmin("right", "change", "&changemodule=".$this->arrModule["modul"],  $this->getLang("commons_module_permissions"), "", "", true, "adminnavi"));
        $arrReturn[] = array("", "");
		$arrReturn[] = array("view", getLinkAdmin($this->arrModule["modul"], "list", "", $this->getLang("commons_list"), "", "", true, "adminnavi"));
		$arrReturn[] = array("right2", getLinkAdmin($this->arrModule["modul"], "new", "", $this->getLang("module_action_new"), "", "", true, "adminnavi"));
		return $arrReturn;
	}

	public function getRequiredFields() {
        $strAction = $this->getAction();
        $arrReturn = array();
        if($strAction == "new") {
            $arrReturn["filemanager_name"] = "string";
            $arrReturn["filemanager_path"] = "folder";
        }
        if($strAction == "edit") {
            $arrReturn["filemanager_name"] = "string";
            $arrReturn["filemanager_path"] = "folder";
        }

        return $arrReturn;
    }


    /**
	 * Returns a list of all repos known to the system
	 *
	 * @return string
     * @autoTestable
     * @permissions view
	 */
	protected function actionList() {

        $objIterator = new class_array_section_iterator(class_module_filemanager_repo::getAllReposCount(_filemanager_show_foreign_));
        $objIterator->setPageNumber($this->getParam("pv"));
        $objIterator->setArraySection(class_module_filemanager_repo::getAllRepos(_filemanager_show_foreign_, $objIterator->calculateStartPos(), $objIterator->calculateEndPos()));

        return $this->renderList($objIterator);
	}

    protected function getNewEntryAction($strListIdentifier) {
        if($this->getObjModule()->rightRight2()) {
            return $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "new", "", $this->getLang("module_action_new"), $this->getLang("module_action_new"), "icon_new.gif"));
        }
    }


    protected function renderAdditionalActions(class_model $objListEntry) {

        if($objListEntry->rightView())
            return array(
                $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "openFolder", "&systemid=".$objListEntry->getSystemid(), "", $this->getLang("repo_oeffnen"), "icon_folderActionOpen.gif"))
            );

        return parent::renderAdditionalActions($objListEntry);
    }


    protected function renderEditAction(class_model $objListEntry) {
        if($objListEntry->rightRight2()) {
            return $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "edit", "&systemid=".$objListEntry->getSystemid(), $this->getLang("repo_bearbeiten"), $this->getLang("repo_bearbeiten"), "icon_folderProperties.gif"));
        }
    }

    protected function renderDeleteAction(interface_model $objListEntry) {
        if($objListEntry->rightRight2())
            return $this->objToolkit->listDeleteButton($objListEntry->getStrName(), $this->getLang("delete_question"), getLinkAdminHref($this->arrModule["modul"], "deleteRepo", "&systemid=".$objListEntry->getSystemid()));

    }


    /**
	 * Deltes a repo or shows the warning box
	 *
	 * @return string "" in case of success
	 */
	protected function actionDeleteRepo() {
		$strReturn = "";
        $objRepo = new class_module_filemanager_repo($this->getSystemid());

		if($objRepo->rightDelete()) {
            $objRepo->deleteObject();

            $this->adminReload(getLinkAdminHref($this->arrModule["modul"]));
		}
		else
			$strReturn = $this->getLang("commons_error_permissions");

		return $strReturn;
	}

	/**
	 * returns the form for a new repo
	 *
	 * @return string
     * @permissions right2
	 */
	protected function actionNew() {
		$strReturn = "";
        //validate Form, if passed
        $bitValidated = true;
        if($this->getParam("repoSaveNew") != "") {
            if(!$this->validateForm()) {
                $bitValidated = false;
                $this->setParam("repoSaveNew", "");
            }
        }

        //save or new?
        if($this->getParam("repoSaveNew") == ""){
            //create the form
            $strReturn .= $this->objToolkit->getValidationErrors($this, "new");
            $strReturn .= $this->objToolkit->formHeader(getLinkAdminHref($this->arrModule["modul"], "new", "repoSaveNew=1"));
            $strReturn .= $this->objToolkit->formInputText("filemanager_name", $this->getLang("commons_name"), $this->getParam("filemanager_name"));
            $strReturn .= $this->objToolkit->formInputText("filemanager_path", $this->getLang("commons_path"), $this->getParam("filemanager_path"), "inputText", getLinkAdminDialog($this->arrModule["modul"], "folderListFolderview", "&form_element=filemanager_path&folder=/files", $this->getLang("commons_open_browser"), $this->getLang("commons_open_browser"), "icon_externalBrowser.gif", $this->getLang("commons_open_browser")));
            $strReturn .= $this->objToolkit->formTextRow($this->getLang("filemanager_upload_filter_h"));
            $strReturn .= $this->objToolkit->formInputText("filemanager_upload_filter", $this->getLang("filemanager_upload_filter"), $this->getParam("filemanager_upload_filter"));
            $strReturn .= $this->objToolkit->formTextRow($this->getLang("filemanager_view_filter_h"));
            $strReturn .= $this->objToolkit->formInputText("filemanager_view_filter", $this->getLang("filemanager_view_filter"), $this->getParam("filemanager_view_filter"));
            $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("commons_save"));
            $strReturn .= $this->objToolkit->formClose();

            $strReturn .= $this->objToolkit->setBrowserFocus("filemanager_name");
        }
        else {
            //save the passed data to database
            $objRepo = new class_module_filemanager_repo();
            $objRepo->setStrName($this->getParam("filemanager_name"));
            $objRepo->setStrPath($this->getParam("filemanager_path"));
            $objRepo->setStrUploadFilter($this->getParam("filemanager_upload_filter"));
            $objRepo->setStrViewFilter($this->getParam("filemanager_view_filter"));
            if(!$objRepo->updateObjectToDb())
                throw new class_exception($this->getLang("fehler_repo"), class_exception::$level_ERROR);

            $this->adminReload(getLinkAdminHref($this->arrModule["modul"]));
        }

		return $strReturn;
	}



	/**
	 * Returns the form to edit the repo and saves the data
	 *
	 * @return string "" in case of success
     * @permissions right2
	 */
	protected function actionEdit() {
		$strReturn = "";
        $objRepo = new class_module_filemanager_repo($this->getSystemid());
		if($objRepo->rightRight2()) {
		    $bitValidated = true;
	        if($this->getParam("repoSaveEdit") != "") {
                if(!$this->validateForm()) {
                    $bitValidated = false;
                    $this->setParam("repoSaveEdit", "");
                }
	        }
			//Form or update?
			if($this->getParam("repoSaveEdit") == "") {

                $strReturn .= $this->objToolkit->getValidationErrors($this, "edit");
				//create the form
    			$strReturn .= $this->objToolkit->formHeader(getLinkAdminHref($this->arrModule["modul"], "edit", "repoSaveEdit=1"));
    			$strReturn .= $this->objToolkit->formInputText("filemanager_name", $this->getLang("commons_name"), $objRepo->getStrName());
    			$strReturn .= $this->objToolkit->formInputText("filemanager_path", $this->getLang("commons_path"), $objRepo->getStrPath(), "inputText", getLinkAdminDialog($this->arrModule["modul"], "folderListFolderview", "&form_element=filemanager_path&folder=/files", $this->getLang("commons_open_browser"), $this->getLang("commons_open_browser"), "icon_externalBrowser.gif", $this->getLang("commons_open_browser")));
    			$strReturn .= $this->objToolkit->formTextRow($this->getLang("filemanager_upload_filter_h"));
    			$strReturn .= $this->objToolkit->formInputText("filemanager_upload_filter", $this->getLang("filemanager_upload_filter"), $objRepo->getStrUploadFilter());
    			$strReturn .= $this->objToolkit->formTextRow($this->getLang("filemanager_view_filter_h"));
    			$strReturn .= $this->objToolkit->formInputText("filemanager_view_filter", $this->getLang("filemanager_view_filter"), $objRepo->getStrViewFilter());
    			$strReturn .= $this->objToolkit->formInputHidden("systemid", $this->getSystemid());
    			$strReturn .= $this->objToolkit->formInputSubmit($this->getLang("commons_save"));
				$strReturn .= $this->objToolkit->formClose();

				$strReturn .= $this->objToolkit->setBrowserFocus("filemanager_name");
			}
			else {
				//Update the databse
				$objRepo = new class_module_filemanager_repo($this->getSystemid());
				$objRepo->setStrName($this->getParam("filemanager_name"));
				$objRepo->setStrPath($this->getParam("filemanager_path"));
				$objRepo->setStrUploadFilter($this->getParam("filemanager_upload_filter"));
				$objRepo->setStrViewFilter($this->getParam("filemanager_view_filter"));


				if($objRepo->updateObjectToDb())
					$strReturn = "";
				else
				    throw new class_exception($this->getLang("repo_bearbeiten_fehler"), class_exception::$level_ERROR);

                $this->adminReload(getLinkAdminHref($this->arrModule["modul"]));
			}
		}
		else
			$strReturn = $this->getLang("commons_error_permissions");

		return $strReturn;
	}





    protected function actionUploadFile() {
        return $this->actionOpenFolder();
    }

	/**
	 * Loads the content of a folder
	 * If requested, loads subactions,too
	 *
	 * @return string
	 */
	protected function actionOpenFolder() {
		$strReturn = "";
        $objRepo = new class_module_filemanager_repo($this->getSystemid());
		if($objRepo->rightView()) {
			//Load the files
			$objFilesystem = new class_filesystem();
            $strExtra = "";

		   	//ok, load the list using the repo-data
		   	$arrViewFilter = array();
		   	if($objRepo->getStrViewFilter() != "")
		   		$arrViewFilter = explode(",", $objRepo->getStrViewFilter());

            $strActions = "";
            if($objRepo->rightRight1()) {
                $strActions .= $this->generateNewFolderDialogCode();
                $strActions .= getLinkAdminManual("href=\"javascript:init_fm_newfolder_dialog();\"", $this->getLang("commons_create_folder"), "", "", "", "", "", "inputSubmit");
                $strActions .= $this->actionUploadFileInternal();
            }
            $strActions .= $this->generateRenameFileDialogCode();

            $arrFiles = $objFilesystem->getCompleteList($this->strFolder, $arrViewFilter, array(".svn"), array(".svn", ".", ".."));

		   	//Building a status-bar, using the toolkit
		   	$arrInfobox = array();
		   	$arrInfobox["folder"] = $this->generatePathNavi($this->strFolder);
		   	$arrInfobox["extraactions"] = $strExtra;
		   	$arrInfobox["files"] = $arrFiles["nrFiles"];
		   	$arrInfobox["folders"] = $arrFiles["nrFolders"];
		   	$arrInfobox["actions"] = $strActions;

		   	$arrInfobox["foldertitle"] = $this->getLang("commons_path");
		   	$arrInfobox["nrfilestitle"] = $this->getLang("nrfilestitle");
		   	$arrInfobox["nrfoldertitle"] = $this->getLang("nrfoldertitle");
		   	$strReturn .= $this->objToolkit->getFilemanagerInfoBox($arrInfobox);
		   	$strReturn .= $this->objToolkit->divider();

			//So, start printing files & folders
			$intI = 0;
			$strReturn .= $this->objToolkit->listHeader();
	  		//Link one folder up?
	  		if($this->strFolderOld != "") {
	  			$strFolderNew = uniSubstr($this->strFolder, 0, uniStrrpos($this->strFolder, "/"));
	  			$strFolderNew = str_replace($objRepo->getStrPath(), "", $strFolderNew);
                $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), "..", "", $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "openFolder", "&systemid=".$this->getSystemid().($strFolderNew != "" ? "&folder=".$strFolderNew : ""), "", $this->getLang("commons_one_level_up"), "icon_folderActionLevelup.gif")), $intI++);
	  		}
	  		else {
	  		    //Link back to the repos
                $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), "..", "", $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "list", "", "", $this->getLang("commons_one_level_up"), "icon_folderActionLevelup.gif")), $intI++);
	  		}
			if(count($arrFiles["folders"]) > 0) {
				foreach($arrFiles["folders"] as $strFolder) {
                    $strAction = "";
		   			$strAction .= $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "openFolder", "&systemid=".$this->getSystemid()."&folder=".$this->strFolderOld."/".$strFolder, "", $this->getLang("repo_oeffnen"), "icon_folderActionOpen.gif"));

    				$objFilesystem = new class_filesystem();
    				$arrFilesSub = $objFilesystem->getCompleteList($this->strFolder."/".$strFolder, array(), array(), array(".", ".."));
    				if(count($arrFilesSub["files"]) == 0 && count($arrFilesSub["folders"]) == 0) {
    					$strAction .= $this->objToolkit->listDeleteButton($strFolder, $this->getLang("ordner_loeschen_frage"), getLinkAdminHref($this->arrModule["modul"], "deleteFolder", "&systemid=".$this->getSystemid()."".($this->strFolderOld!= "" ? "&folder=".$this->strFolderOld: "")."&delFolder=".$strFolder));
    				}
    				else
    					$strAction .= $this->objToolkit->listButton(getImageAdmin("icon_tonDisabled.gif", $this->getLang("ordner_loeschen_fehler_l")));

                    $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), $strFolder, getImageAdmin("icon_folderOpen.gif"), $strAction, $intI++, (_filemanager_foldersize_ != "false" ? bytesToString($objFilesystem->folderSize($this->strFolder."/".$strFolder, $arrViewFilter)) : ""));
				}
			}
			$strReturn .= $this->objToolkit->listFooter();
            $strReturn .= $this->objToolkit->divider();
            //For the files, we have to build a data table
            $arrHeader = array();
            $arrHeader[0] = "&nbsp;";
            $arrHeader[1] = "&nbsp;";
            $arrHeader[2] = $this->getLang("datei_groesse");
            $arrHeader[3] = $this->getLang("datei_erstell");
            $arrHeader[4] = $this->getLang("datei_bearbeit");
            $arrHeader[5] = "";
            $arrFilesTemplate = array();
	  		if(count($arrFiles["files"]) > 0) {
	  		    $intJ = 0;
				foreach($arrFiles["files"] as $arrOneFile) {
					//Geticon
					$arrMime  = $this->objToolkit->mimeType($arrOneFile["filename"]);
					$strFilename = $arrOneFile["filename"];
					$bitImage = false;
					if($arrMime[1] == "jpg" || $arrMime[1] == "png" || $arrMime[1] == "gif")
					   $bitImage = true;

					//Filename too long?
					$strFilename = uniStrTrim($strFilename, 35);

					$strActions = "";
					if(!$bitImage)
		   			    $strActions .= $this->objToolkit->listButton(getLinkAdminRaw(_webpath_.$this->strFolder."/".$arrOneFile["filename"], "",$this->getLang("datei_oeffnen"), "icon_lens.gif", "_blank" ));
		   			else
		   			    $strActions .= $this->objToolkit->listButton(getLinkAdminDialog($this->arrModule["modul"], "imageDetails", "&systemid=".$this->getSystemid().($this->strFolderOld != "" ? "&folder=".$this->strFolderOld : "" )."&file=".$arrOneFile["filename"], "", $this->getLang("datei_oeffnen"), "icon_crop.gif"));

		   			$strActions .= $this->objToolkit->listButton(getLinkAdminManual("href=\"javascript:init_fm_renameFile_dialog('".$arrOneFile["filename"]."');\"", $this->getLang("datei_umbenennen"), $this->getLang("datei_umbenennen"), "icon_pencil.gif"));
		   			$strActions .= $this->objToolkit->listDeleteButton($arrOneFile["filename"], $this->getLang("datei_loeschen_frage"), getLinkAdminHref($this->arrModule["modul"], "deleteFile", "&systemid=".$this->getSystemid()."".($this->strFolderOld != "" ? "&folder=".$this->strFolderOld: "")."&file=".$arrOneFile["filename"]));

		   			// if an image, attach a thumbnail-tooltip
		   			if ($bitImage) {
		   			    $strImage = "<div class=\'loadingContainer\'><img src=\\'"._webpath_."/image.php?image=".urlencode(str_replace(_realpath_, "", $arrOneFile["filepath"]))."&amp;maxWidth=100&amp;maxHeight=100\\' /></div>";
		   			    $arrFilesTemplate[$intJ][0] = getImageAdmin($arrMime[2], $strImage, true);
		   			} else
		   			    $arrFilesTemplate[$intJ][0] = getImageAdmin($arrMime[2], $arrMime[0]);

					$arrFilesTemplate[$intJ][1] = $strFilename;
					$arrFilesTemplate[$intJ][2] = bytesToString($arrOneFile["filesize"]);
					$arrFilesTemplate[$intJ][3] = timeToString($arrOneFile["filecreation"]);
					$arrFilesTemplate[$intJ][4] = timeToString($arrOneFile["filechange"]);
					$arrFilesTemplate[$intJ++][5] = "<div class=\"actions\">".$strActions."</div>";
				}
	  		}
	  		$strReturn .= $this->objToolkit->dataTable($arrHeader, $arrFilesTemplate);
		}
		else
			$this->getLang("commons_error_permissions");

		return $strReturn;
	}


    /**
     * Loads the content of a folder
     * If requested, loads subactions,too
     *
     * SPECIAL MODE FOR MODULE FOLDERVIEW
     *
     * @return string
     * @permissions view
     */
	protected function actionFolderContentFolderviewMode() {
		$strReturn = "";

        //if set, save CKEditors CKEditorFuncNum parameter to read it again in KAJONA.admin.folderview.selectCallback()
        //so we don't have to pass through the param with all requests
	    if ($this->getParam("CKEditorFuncNum") != "") {
            $strReturn .= "<script type=\"text/javascript\">window.opener.KAJONA.admin.folderview.selectCallbackCKEditorFuncNum = ".(int)$this->getParam("CKEditorFuncNum").";</script>";
        }


        $strTargetfield = $this->getParam("form_element");

        $this->setArrModuleEntry("template", "/folderview.tpl");

		//list repos or contents?
		if($this->getSystemid() == "") {
            //Load the repos
            $arrObjRepos = class_module_filemanager_repo::getAllRepos(_filemanager_show_foreign_);
            $intI = 0;
            //Print every repo
            /** @var class_module_filemanager_repo $objOneRepo */
            foreach($arrObjRepos as $objOneRepo) {
                //check rights
                if($objOneRepo->rightView()) {
                    $strActions = "";
                    if($objOneRepo->rightView())
                        $strActions .= $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "folderContentFolderviewMode", "&form_element=".$strTargetfield."&systemid=".$objOneRepo->getSystemid(), "", $this->getLang("repo_oeffnen"), "icon_folderActionOpen.gif"));

                    $strReturn .= $this->objToolkit->simpleAdminList($objOneRepo, $strActions, $intI++);
                }
            }

            if(uniStrlen($strReturn) != 0)
                $strReturn = $this->objToolkit->listHeader().$strReturn.$this->objToolkit->listFooter();

            if(count($arrObjRepos) == 0)
                $strReturn .= $this->getLang("liste_leer");
		}
		else {
            $objRepo = new class_module_filemanager_repo($this->getSystemid());
    		if($objRepo->rightView()) {
    			//Load the files
    			$objFilesystem = new class_filesystem();
    			$strAddonAction = $this->getParam("fmcommand");

    		   	//ok, load the list using the repo-data
    		   	$arrViewFilter = array();
    		   	if($objRepo->getStrViewFilter() != "")
    		   		$arrViewFilter = explode(",", $objRepo->getStrViewFilter());

                $strActions = "";
                if($strAddonAction == "") {
                    $strActions .= $this->generateNewFolderDialogCode();
                    $strActions .= getLinkAdminManual("href=\"javascript:init_fm_newfolder_dialog();\"", $this->getLang("commons_create_folder"), "", "", "", "", "", "inputSubmit");
                    $strActions .= $this->actionUploadFileInternal();
                }
    		   	$arrFiles = $objFilesystem->getCompleteList($this->strFolder, $arrViewFilter, array(".svn"), array(".svn", ".", ".."));

    		   	//Building a status-bar, using the toolkit
    		   	$arrInfobox = array();
    		   	$arrInfobox["folder"] = $this->strFolder;
    		   	//$arrInfobox["extraactions"] = $strExtra;
    		   	$arrInfobox["files"] = $arrFiles["nrFiles"];
    		   	$arrInfobox["folders"] = $arrFiles["nrFolders"];
    		   	$arrInfobox["actions"] = $strActions;

    		   	$arrInfobox["foldertitle"] = $this->getLang("commons_path");
    		   	$arrInfobox["nrfilestitle"] = $this->getLang("nrfilestitle");
    		   	$arrInfobox["nrfoldertitle"] = $this->getLang("nrfoldertitle");
    		   	$strReturn .= $this->objToolkit->getFilemanagerInfoBox($arrInfobox);


    			//So, start printing files & folders
    			$intI = 0;

    		    $strReturn .= $this->objToolkit->divider();
        		$strReturn .= $this->objToolkit->listHeader();
          		//Link one folder up?
          		if($this->strFolderOld != "") {
          			$strFolderNew = uniSubstr($this->strFolder, 0, uniStrrpos($this->strFolder, "/"));
          			$strFolderNew = str_replace($objRepo->getStrPath(), "", $strFolderNew);
                    $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), "..", getImageAdmin("icon_folderOpen.gif"), $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "folderContentFolderviewMode", "&form_element=".$strTargetfield."&systemid=".$this->getSystemid().($strFolderNew != "" ? "&folder=".$strFolderNew : ""), "", $this->getLang("commons_one_level_up"), "icon_folderActionLevelup.gif")), $intI++);
          		}
          		else {
          		    //Link up to repo list
          		    $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), "..", getImageAdmin("icon_folderOpen.gif"), $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "folderContentFolderviewMode", "&form_element=".$strTargetfield, "", $this->getLang("commons_one_level_up"), "icon_folderActionLevelup.gif")), $intI++);
          		}
        		if(count($arrFiles["folders"]) > 0) {
        			foreach($arrFiles["folders"] as $strFolder) {
                            $strAction = "";
        	   			$strAction .= $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "folderContentFolderviewMode", "&form_element=".$strTargetfield."&systemid=".$this->getSystemid()."&folder=".$this->strFolderOld."/".$strFolder, "", $this->getLang("repo_oeffnen"), "icon_folderActionOpen.gif"));
        	   			$strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), $strFolder, getImageAdmin("icon_folderOpen.gif"), $strAction, $intI++, (_filemanager_foldersize_ != "false" ? bytesToString($objFilesystem->folderSize($this->strFolder."/".$strFolder, $arrViewFilter)) : ""));
        			}
        		}

        		$strReturn .= $this->objToolkit->listFooter();
                $strReturn .= $this->objToolkit->divider();
                //For the files, we have to build a data table
                $arrHeader = null;
                $arrFilesTemplate = array();

                if(count($arrFiles["files"]) > 0) {
          		    $intJ = 0;
        			foreach($arrFiles["files"] as $arrOneFile) {
        				//Get icon
        				$arrMime  = $this->objToolkit->mimeType($arrOneFile["filename"]);

        				$bitImage = false;
				        if($arrMime[1] == "jpg" || $arrMime[1] == "png" || $arrMime[1] == "gif")
				           $bitImage = true;

        				$strFilename = $arrOneFile["filename"];
        				//Filename too long?
        				$strFilename = uniStrTrim($strFilename, 40);

        				$strActions = "";

                        $strFolder = $this->strFolder;

                        //add image.php if it's an image and file will be passed to CKEditor
                        //further processing is done in processWysiwygHtmlContent() when saving the content edited via CKEditor
                        $strValue = "";
                        if ($bitImage && $strTargetfield == "ckeditor") {
        	   			     $strValue = _webpath_."/image.php?image=".$strFolder."/".$arrOneFile["filename"];
                        } else {
                            $strValue = _webpath_.$strFolder."/".$arrOneFile["filename"];
                        }
        	   			$strActions .= $this->objToolkit->listButton("<a href=\"#\" title=\"".$this->getLang("useFile")."\" onmouseover=\"KAJONA.admin.tooltip.add(this);\" onclick=\"KAJONA.admin.folderview.selectCallback([['".$strTargetfield."', '".$strValue."']]);\">".getImageAdmin("icon_accept.gif"));

			   			// if an image, attach a thumbnail-tooltip
			   			if ($bitImage) {
			   			    $strImage = "<div class=\'loadingContainer\'><img src=\\'"._webpath_."/image.php?image=".urlencode($strFolder."/".$arrOneFile["filename"])."&amp;maxWidth=100&amp;maxHeight=100\\' /></div>";
			   			    $arrFilesTemplate[$intJ][0] = getImageAdmin($arrMime[2], $strImage, true);
			   			} else
			   			    $arrFilesTemplate[$intJ][0] = getImageAdmin($arrMime[2], $arrMime[0]);

        				$arrFilesTemplate[$intJ][1] = $strFilename;
        				$arrFilesTemplate[$intJ][2] = bytesToString($arrOneFile["filesize"]);
        				$arrFilesTemplate[$intJ++][3] = "<div class=\"actions\">".$strActions."</div>";
        			}
          		}
          		$strReturn .= $this->objToolkit->dataTable($arrHeader, $arrFilesTemplate);
    		}
    		else
    			$this->getLang("commons_error_permissions");
		}

		return $strReturn;
	}


    /**
     * Generates a view to browse the filesystem directly
     * @return string
     */
    protected function actionFolderListFolderview() {

        $this->setArrModuleEntry("template", "/folderview.tpl");
        $strReturn = "";

        //param inits
        $strFolder = "/files/images";
        if($this->getParam("folder") != "")
            $strFolder = $this->getParam("folder");

        $arrSuffix = array();
        if($this->getParam("suffix") != "")
            $arrSuffix = explode("|", $this->getParam("suffix"));

        $arrExclude = array();
        if($this->getParam("exclude") != "")
            $arrExclude = explode("|", $this->getParam("exclude"));

        $bitFolder = true;
        if($this->getParam("bit_folder") != "")
            $bitFolder = $this->getParam("bit_folder");

        $bitFile = true;
        if($this->getParam("bit_file") != "")
            $bitFile = $this->getParam("bit_file");

        $arrExcludeFolder = array(0 => ".", 1 => "..");
        if($this->getParam("exclude_folder") != "")
            $arrExcludeFolder = explode("|", $this->getParam("exclude_folder"));

        $strFormElement = "bild";
        if($this->getParam("form_element") != "")
            $strFormElement = $this->getParam("form_element");

        //if set, save CKEditors CKEditorFuncNum parameter to read it again in KAJONA.admin.folderview.selectCallback()
        //so we don't have to pass through the param with all requests
	    if ($this->getParam("CKEditorFuncNum") != "") {
            $strReturn .= "<script type=\"text/javascript\">window.opener.KAJONA.admin.folderview.selectCallbackCKEditorFuncNum = ".(int)$this->getParam("CKEditorFuncNum").";</script>";
        }

        $objFilesystem = new class_filesystem();
		$arrContent = $objFilesystem->getCompleteList($strFolder, $arrSuffix, $arrExclude, $arrExcludeFolder, $bitFolder, false);

		$strReturn .= $this->objToolkit->listHeader();
		$strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), $this->getLang("commons_path"), "", $strFolder, 1);
		$strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), $this->getLang("ordner_anz"), "", $arrContent["nrFolders"], 1);
		$strReturn .= $this->objToolkit->listFooter();
		$strReturn .= $this->objToolkit->divider();

        $intCounter = 0;
		//Show Folders
		//Folder to jump one back up
		$arrFolderStart = array("/portal");
		$bitHit = false;
		if(!in_array($strFolder, $arrFolderStart) && $bitHit == false) {
			$strReturn .= $this->objToolkit->listHeader();
			$strAction = $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "folderListFolderview", "&folder=".uniSubstr($strFolder, 0, uniStrrpos($strFolder, "/"))."&suffix=".implode("|", $arrSuffix)."&exclude=".implode("|", $arrExclude)."&bit_folder=".$bitFolder."&bit_file=".$bitFile."&form_element=".$strFormElement, $this->getLang("commons_one_level_up"), $this->getLang("commons_one_level_up"), "icon_folderActionLevelup.gif"));
			$strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), "..", getImageAdmin("icon_folderOpen.gif"), $strAction, $intCounter++);
			$bitHit = true;
		}
		if($arrContent["nrFolders"] != 0) {
			if(!$bitHit)
				$strReturn .= $this->objToolkit->listHeader();
			$bitHit = true;
			foreach($arrContent["folders"] as $strFolderCur) {
				$strAction = $this->objToolkit->listButton(getLinkAdmin($this->arrModule["modul"], "folderListFolderview", "&folder=".$strFolder."/".$strFolderCur."&suffix=".implode("|", $arrSuffix)."&exclude=".implode("|", $arrExclude)."&bit_folder=".$bitFolder."&bit_file=".$bitFile."&form_element=".$strFormElement, $this->getLang("ordner_oeffnen"), $this->getLang("ordner_oeffnen"), "icon_folderActionOpen.gif"));
				$strAction .= $this->objToolkit->listButton("<a href=\"#\" title=\"".$this->getLang("ordner_uebernehmen")."\" onmouseover=\"KAJONA.admin.tooltip.add(this);\" onclick=\"KAJONA.admin.folderview.selectCallback([['".$strFormElement."', '".$strFolder."/".$strFolderCur."']]);\">".getImageAdmin("icon_accept.gif"));
                $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), $strFolderCur, getImageAdmin("icon_folderOpen.gif"), $strAction, $intCounter++);
			}
		}
		if($bitHit)
		  $strReturn .= $this->objToolkit->listFooter();

        return $strReturn;
    }

	/**
	 * Shows the form to delete a file / deletes a file
	 *
	 * @return string "" in case of success
	 */
	protected function actionDeleteFile() {
		$strReturn = "";
		//Rights
        $objCommon = new class_module_system_common($this->getSystemid());
		if($objCommon->rightDelete()) {
			$objFilesystem = new class_filesystem();
			if(!$objFilesystem->fileDelete($this->strFolder."/".$this->getParam("file")))
				$strReturn .= $this->getLang("datei_loeschen_fehler");
            else {
                if($this->getParam("galleryId") != "")
                    $this->adminReload(getLinkAdminHref("gallery", "showGallery", "systemid=".$this->getParam("galleryId")."&resync=true"));
                else
                    $this->adminReload(getLinkAdminHref($this->arrModule["modul"], "openFolder", "systemid=".$this->getSystemid()."&folder=".$this->getParam("folder")));
            }

		}
		else
			$strReturn = $this->getLang("commons_error_permissions");

		return $strReturn;
	}

	/**
	 * Deletes a folder, if empty, shows the warning
	 *
	 * @return string "" in case of success
	 */
	protected function actionDeleteFolder() {
		$strReturn = "";
		//Rights
        $objCommon = new class_module_system_common($this->getSystemid());
		if($objCommon->rightDelete()) {
			$objFilesystem = new class_filesystem();

			if(!$objFilesystem->folderDelete($this->strFolder."/".$this->getParam("delFolder")))
				$strReturn .= $this->getLang("ordner_loeschen_fehler");
            else
                $this->adminReload(getLinkAdminHref($this->arrModule["modul"], "openFolder", "systemid=".$this->getSystemid()."&folder=".$this->getParam("folder")));
		}
		else
			$strReturn = $this->getLang("commons_error_permissions");

		return $strReturn;
	}


    /**
     * Generates the code to delete a folder via ajax
     * @return string
     */
    private function generateNewFolderDialogCode() {
        $strReturn = "";

        //Build code for create-dialog
		$strDialog = $this->objToolkit->formInputText("folderName", $this->getLang("ordner_name"));

        $strReturn .= "<script type=\"text/javascript\">\n
                        function init_fm_newfolder_dialog() {
                            jsDialog_1.setTitle('".$this->getLang("ordner_anlegen_dialogHeader")."');
                            jsDialog_1.setContent('".uniStrReplace(array("\r\n", "\n"), "", addslashes($strDialog))."',
                                                  '".$this->getLang("commons_create_folder")."',
                                                  'javascript:KAJONA.admin.filemanager.createFolder(\'folderName\', \'".$this->getSystemid()."\', \'".$this->strFolderOld."\', \'\', \'\' ); jsDialog_1.hide();');
                                    jsDialog_1.init(); }\n
                      ";

        $strReturn .= "</script>";
        $strReturn .= $this->objToolkit->jsDialog(1);
        return $strReturn;
    }


    /**
     * Generates the code to delete renaming of a file to ajax calls
     * @return string
     */
    private function generateRenameFileDialogCode() {
        $strReturn = "";

        //Build code for create-dialog
		$strDialog = $this->objToolkit->formInputText("fileName", $this->getLang("datei_name"));

        $strReturn .= "<script type=\"text/javascript\">\n

                        function init_fm_renameFile_dialog(strFilename) {
                            jsDialog_1.setTitle('".$this->getLang("datei_umbenennen")."');
                            jsDialog_1.setContent('".uniStrReplace(array("\r\n", "\n"), "", addslashes($strDialog))."',
                                                  '".$this->getLang("rename")."',
                                                  'javascript:KAJONA.admin.filemanager.renameFile(\'fileName\', \'".$this->getSystemid()."\', \'".$this->strFolderOld."\', \''+strFilename+'\', \'\', \'\' ); jsDialog_1.hide();');
                                                                                       //strInputId,          strRepoId,                     strRepoFolder, strOldName, strSourceModule, strSourceAction
                                    jsDialog_1.init();
                            document.getElementById('fileName').value = strFilename;

                            }\n
                      ";

        $strReturn .= "</script>";
        $strReturn .= $this->objToolkit->jsDialog(1);
        return $strReturn;
    }


	/**
	 * Uploads or shows the form to upload a file
	 *
	 * @return string
	 */
	private function actionUploadFileInternal() {
		$strReturn = "";
        $objRepo = new class_module_filemanager_repo($this->getSystemid());
		if($objRepo->rightRight1()) {
			//Upload-Form

    	    $strReturn .= $this->objToolkit->formHeader(getLinkAdminHref($this->arrModule["modul"], $this->getAction(), "datei_upload_final=1"), "formUpload", "multipart/form-data");
			$strReturn .= $this->objToolkit->formInputHidden("systemid", $this->getSystemid());
			$strReturn .= $this->objToolkit->formInputHidden("folder", $this->strFolderOld);

			$strReturn .= $this->objToolkit->formInputHidden("flashuploadSystemid", $this->getSystemid());
            $strReturn .= $this->objToolkit->formInputHidden("flashuploadFolder", $this->strFolderOld);

			$strReturn .= $this->objToolkit->formInputUploadFlash("filemanager_upload", $this->getLang("filemanager_upload"), $objRepo->getStrUploadFilter(), true, true);
			$strReturn .= $this->objToolkit->formClose();

			if($this->getParam("datei_upload_final") != "") {
				//Handle the fileupload
				$arrSource = $this->getParam("filemanager_upload");

                $strTarget = $this->strFolder."/".createFilename($arrSource["name"]);
                $objFilesystem = new class_filesystem();
                //Check file for correct filters
                $arrAllowed = explode(",", $objRepo->getStrUploadFilter());
                $strSuffix = uniStrtolower(uniSubstr($arrSource["name"], uniStrrpos($arrSource["name"], ".")));
                if($objRepo->getStrUploadFilter() == "" || in_array($strSuffix, $arrAllowed)) {
                    if($objFilesystem->copyUpload($strTarget, $arrSource["tmp_name"])) {
                        $strReturn .= $this->getLang("upload_erfolg");

                        class_logger::getInstance()->addLogRow("uploaded file ".$strTarget, class_logger::$levelInfo);
                    }
                    else
                        $strReturn .= $this->getLang("upload_fehler");
                }
                else {
                    @unlink($arrSource["tmp_name"]);
                    $strReturn .= $this->getLang("upload_fehler_filter");
                }
			}
		}
		else
			$strReturn = $this->getLang("commons_error_permissions");

		return $strReturn;
	}


	/**
	 * Returns details and additional functions handling the current image.
	 *
	 * @return string
	 */
	protected function actionImageDetails() {
		$strReturn = "";

        $strPlainImage = "";

        //overlay-mode
        $this->setArrModuleEntry("template", "/folderview.tpl");

        //see, if there was an image passed directly
        $strFile = $this->getParam("imageFile");
        if($strFile != "") {
            $strFile = uniStrReplace(_webpath_, "", $strFile);
            $strPlainImage = $strFile;
            $strFile = _realpath_.$strFile;

            $this->setArrModuleEntry("template", "/folderview.tpl");
        }




        $arrTemplate = array();

        if($strFile == "") {
            $strFile = _realpath_.(substr($this->strFolder, 0, 1) == "/" ? "" : "/").$this->strFolder."/".$this->getParam("file");
            $strPlainImage =  (substr($this->strFolder, 0, 1) == "/" ? "" : "/").$this->strFolder."/".$this->getParam("file");
        }
		if(is_file($strFile)) {

			$objFilesystem = new class_filesystem();
			$arrDetails = $objFilesystem->getFileDetails($strFile);

			$arrTemplate["file_name"] = $arrDetails["filename"];
			$arrTemplate["file_path"] = $arrDetails["filepath"];
			$arrTemplate["file_path_title"] = $this->getLang("commons_path");

			$arrSize = getimagesize($strFile);
			$arrTemplate["file_dimensions"] = $arrSize[0]." x ".$arrSize[1];
            $arrTemplate["file_dimensions_title"] = $this->getLang("bild_groesse");

            $arrTemplate["file_size"] = bytesToString($arrDetails["filesize"]);
            $arrTemplate["file_size_title"] = $this->getLang("datei_groesse");

            $arrTemplate["file_lastedit"] = timeToString($arrDetails["filechange"]);
            $arrTemplate["file_lastedit_title"] = $this->getLang("datei_bearbeit");

			//Generate Dimensions
			$intHeight = $arrSize[1];
			$intWidth = $arrSize[0];

			while($intWidth > 500 || $intHeight > 400) {
				$intWidth *= 0.8;
				$intHeight *= 0.8;
			}
			//Round
			$intWidth = number_format($intWidth, 0);
			$intHeight = number_format($intHeight, 0);
			$arrTemplate["file_image"] = "<img src=\""._webpath_."/image.php?image=".urlencode(str_replace(_realpath_, "", $strFile))."&amp;maxWidth=".$intWidth."&amp;maxHeight=".$intHeight."\" id=\"fm_filemanagerPic\" />";

            $arrTemplate["file_actions"] = "";

            $arrTemplate["file_actions"] .= $this->objToolkit->listButton(getLinkAdminManual("href=\"#\" onclick=\"KAJONA.admin.filemanager.imageEditor.showRealSize(); return false;\"", "", $this->getLang("showRealsize"), "icon_zoom_in.gif"));
            $arrTemplate["file_actions"] .= $this->objToolkit->listButton(getLinkAdminManual("href=\"#\" onclick=\"KAJONA.admin.filemanager.imageEditor.showPreview(); return false;\"", "", $this->getLang("showPreview"), "icon_zoom_out.gif"))." ";
            $arrTemplate["file_actions"] .= $this->objToolkit->listButton(getLinkAdminManual("href=\"#\" onclick=\"KAJONA.admin.filemanager.imageEditor.rotate(90); return false;\"", "", $this->getLang("rotateImageLeft"), "icon_rotate_left.gif"));
            $arrTemplate["file_actions"] .= $this->objToolkit->listButton(getLinkAdminManual("href=\"#\" onclick=\"KAJONA.admin.filemanager.imageEditor.rotate(270); return false;\"", "", $this->getLang("rotateImageRight"), "icon_rotate_right.gif"))." ";
            $arrTemplate["file_actions"] .= $this->objToolkit->listButton(getLinkAdminManual("href=\"#\" onclick=\"KAJONA.admin.filemanager.imageEditor.showCropping(); return false;\"", "", $this->getLang("cropImage"), "icon_crop.gif"));
            $arrTemplate["file_actions"] .= $this->objToolkit->listButton(getLinkAdminManual("href=\"#\" onclick=\"KAJONA.admin.filemanager.imageEditor.saveCropping(); return false;\"", "", $this->getLang("cropImageAccept"), "icon_crop_acceptDisabled.gif", "accept_icon"))." ";


            $arrTemplate["filemanager_image_js"] = "<script type=\"text/javascript\">
                KAJONA.admin.loader.loadImagecropperBase();

                var fm_image_rawurl = '"._webpath_."/image.php?image=".urlencode(str_replace(_realpath_, "", $strFile))."&quality=80';
                var fm_image_scaledurl = '"._webpath_."/image.php?image=".urlencode(str_replace(_realpath_, "", $strFile))."&maxWidth=__width__&maxHeight=__height__';
                var fm_image_scaledMaxWidth = $intWidth;
                var fm_image_scaledMaxHeight = $intHeight;
                var fm_image_isScaled = true;
                var fm_repo_id = '".$this->getSystemid()."';
                var fm_file = '".$strPlainImage."' ;
                var fm_folder = '".$this->getParam("folder")."';
                var fm_warning_unsavedHint = '".$this->getLang("cropWarningUnsavedHint")."';

                function init_fm_crop_save_warning_dialog() { jsDialog_1.setTitle('".$this->getLang("cropWarningDialogHeader")."'); jsDialog_1.setContent('".$this->getLang("cropWarningSaving")."', '".$this->getLang("cropWarningCrop")."', 'javascript:KAJONA.admin.filemanager.imageEditor.saveCroppingToBackend()'); jsDialog_1.init(); }
                function init_fm_screenlock_dialog() { jsDialog_3.init(); }
                function hide_fm_screenlock_dialog() { jsDialog_3.hide(); }

				</script>";

			$arrTemplate["filemanager_image_js"] .= $this->objToolkit->jsDialog(1);
			$arrTemplate["filemanager_image_js"] .= $this->objToolkit->jsDialog(3);

            $arrTemplate["filemanager_internal_code"] = "<input type=\"hidden\" name=\"fm_int_realwidth\" id=\"fm_int_realwidth\" value=\"".$arrSize[0]."\" />";
            $arrTemplate["filemanager_internal_code"] .= "<input type=\"hidden\" name=\"fm_int_realheight\" id=\"fm_int_realheight\" value=\"".$arrSize[1]."\" />";
            $arrTemplate["filemanager_internal_code"] .= "<input type=\"hidden\" name=\"galleryId\" id=\"galleryId\" value=\"".$this->getParam("galleryId")."\" />";

		}
		$strReturn .= $this->objToolkit->getFilemanagerImageDetails($arrTemplate);
		return $strReturn;
	}




	/**
	 * Determines the current folder
	 *
	 */
	private function getCurrentFolder() {
		$objRepo = new class_module_filemanager_repo($this->getSystemid());
		//Check, which level should be loaded. Remind the evil ones!
		if($this->getParam("folder") != "")
			$strFolder = $this->getParam("folder");
		else
			$strFolder = "";

		//Check
		$strFolder = htmlspecialchars($strFolder);
		//Avoid jumps
		$strFolder = str_replace("../", "", $strFolder);
		//Add to the repo-path
		$this->strFolderOld = $strFolder;
		$this->strFolder = ($objRepo->getStrPath() != "" ? $objRepo->getStrPath() : "").$strFolder;
	}


	/**
	 * Generates a path-navigation
	 *
	 * @param string $strPath
	 * @return string
	 */
	private function generatePathNavi($strPath) {
        $arrPaths = array();
        $objRepo = new class_module_filemanager_repo($this->getSystemid());

        //remove repo-folder
        $strPath = uniStrReplace($objRepo->getStrPath(), "", $strPath);

        //remove first /
        if(isset($strPath[0]) && $strPath[0] == "/") {
            $strPath = uniSubstr($strPath, 1);
        }

        //the first entry is the repo itself
        $arrPaths[] = getLinkAdmin($this->arrModule["modul"], "openFolder", "&systemid=".$this->getSystemid(), $objRepo->getStrPath());

        if(uniStrlen($strPath) > 0 ) {
            $arrTempFolders = explode("/", $strPath);

            $strRealFolder = "";
            foreach ($arrTempFolders as $intKey => $strOneFolder) {
                //get the name of the current folder
                $strFoldername = $strOneFolder;
                $strRealFolder .= "/".$strOneFolder;
                $arrPaths[] = getLinkAdmin($this->arrModule["modul"], "openFolder", "&folder=".$strRealFolder."&systemid=".$this->getSystemid(), $strFoldername);
                //remove the current folder
                $strPath = uniSubstr($strPath, 0, uniStrrpos($strPath, "/"));
            }
        }

        return $this->objToolkit->getPathNavigation($arrPaths);
	}


}
