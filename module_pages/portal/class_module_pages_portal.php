<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                            *
********************************************************************************************************/

/**
 * Handles the loading of the pages - loads the elements, passes control to them and returns the complete
 * page ready for output
 *
 * @package module_pages
 * @author sidler@mulchprod.de
 */
class class_module_pages_portal extends class_portal implements interface_portal {

    /**
     * Static field storing the last registered page-title. Modules may register additional page-titles in order
     * to have them places as the current page-title. Since this is a single field, the last module wins in case of
     * multiple entries.
     * @var string
     */
    private static $strAdditionalTitle = "";

	public function __construct($arrElementData) {

		parent::__construct($arrElementData);

        $this->setArrModuleEntry("modul", "pages");
        $this->setArrModuleEntry("moduleId", _pages_modul_id_);

        $this->setAction("generatePage");

	}

	/**
	 * Handles the loading of a page, more in a functional than in an oop style
	 *
     * @return string the generated page
     * @permissions view
	 */
	protected function actionGeneratePage() {

		//Determin the pagename
		$strPagename = $this->getPagename();

		//Load the data of the page
        $objPageData = class_module_pages_page::getPageByName($strPagename);


		//check, if the page is enabled and if the rights are given, or if we want to load a preview of a page
		$bitErrorpage = false;
        if($objPageData->getStrName() == "" || ($objPageData->getStatus() != 1 || !$objPageData->rightView()))
			$bitErrorpage = true;


		//but: if count != 0 && preview && rights:
		if($bitErrorpage && $objPageData->getStrName() != "" && $this->getParam("preview") == "1" && $objPageData->rightEdit())
			$bitErrorpage = false;


		//check, if the template could be loaded
		try {
		    $strTemplateID = $this->objTemplate->readTemplate("/module_pages/".$objPageData->getStrTemplate(), "", false, true);
		}
		catch (class_exception $objException) {
            $bitErrorpage = true;
		}

		if($bitErrorpage) {
			//Unfortunately, we have to load the errorpage

			//try to send the correct header
			//page not found
            if($objPageData->getStrName() == "" || $objPageData->getStatus() != 1)
			    header(class_http_statuscodes::$strSC_NOT_FOUND);

			//user is not allowed to view the page
			if($objPageData->getStrName() != "" && !$objPageData->rightView())
			    header(class_http_statuscodes::$strSC_FORBIDDEN);


            //check, if the page may be loaded using the default-language
            $strPreviousLang = $this->getStrPortalLanguage();
            $objDefaultLang = class_module_languages_language::getDefaultLanguage();
            if($this->getStrPortalLanguage() != $objDefaultLang->getStrName()) {
                class_logger::getInstance()->addLogRow("Requested page ".$strPagename." not existing in language ".$this->getStrPortalLanguage().", switch to fallback lang", class_logger::$levelWarning);
                $objDefaultLang->setStrPortalLanguage($objDefaultLang->getStrName());
                $objPageData = class_module_pages_page::getPageByName($strPagename);

                try {
                    $strTemplateID = $this->objTemplate->readTemplate("/module_pages/".$objPageData->getStrTemplate(), "", false, true);
                }
                catch (class_exception $objException) {
                    $strPagename = _pages_errorpage_;
                    //revert to the old language - fallback didn't work
                    $objDefaultLang->setStrPortalLanguage($strPreviousLang);
                }
            }
            else
                $strPagename = _pages_errorpage_;


			$objPageData = class_module_pages_page::getPageByName($strPagename);

			//check, if the page is enabled and if the rights are given, too
			if($objPageData->getStrName() == "" || ($objPageData->getStatus() != 1 || !$objPageData->rightView())) {
				//Whoops. Nothing to output here
				throw new class_exception("Requested Page ".$strPagename." not existing, no errorpage created or set!", class_exception::$level_FATALERROR);
				return;
			}


		}

		//react on portaleditor commands
        //pe to display, or pe to disable?
        if($this->getParam("pe") == "false") {
            $this->objSession->setSession("pe_disable", "true");
        }
        if($this->getParam("pe") == "true") {
            $this->objSession->setSession("pe_disable", "false");
        }

        //if using the pe, the cache shouldn't be used, otherwise strange things might happen.
		//the system could frighten your cat or eat up all your cheese with marshmellows...
        //get the current state of the portal editor
		$bitPeRequested = false;
        if(_pages_portaleditor_ == "true" && $this->objSession->getSession("pe_disable") != "true" && $this->objSession->isAdmin() && $objPageData->rightEdit()) {
            $bitPeRequested = true;
		}

		//If we reached up till here, we can begin loading the elements to fill
		$arrElementsOnPage = array();

        if($bitPeRequested)
            $arrElementsOnPage = class_module_pages_pageelement::getElementsOnPage($objPageData->getSystemid(), false, $this->getStrPortalLanguage());
        else
            $arrElementsOnPage = class_module_pages_pageelement::getElementsOnPage($objPageData->getSystemid(), true, $this->getStrPortalLanguage());
		//If there's a master-page, load elements on that, too
		$objMasterData = class_module_pages_page::getPageByName("master");
        $bitEditPermissionOnMasterPage = false;
		if($objMasterData->getStrName() != "") {
            $arrElementsOnMaster = array();
            if($bitPeRequested)
                $arrElementsOnMaster = class_module_pages_pageelement::getElementsOnPage($objMasterData->getSystemid(), false, $this->getStrPortalLanguage());
            else
                $arrElementsOnMaster = class_module_pages_pageelement::getElementsOnPage($objMasterData->getSystemid(), true, $this->getStrPortalLanguage());
			//and merge them
			$arrElementsOnPage = array_merge($arrElementsOnPage, $arrElementsOnMaster);
            if($objMasterData->rightEdit())
                $bitEditPermissionOnMasterPage = true;

		}

		//Load the template from the filesystem to get the placeholders
        $strTemplateID = $this->objTemplate->readTemplate("/module_pages/".$objPageData->getStrTemplate(), "", false, true);
        //bit include the masters-elements!!
        $arrRawPlaceholders = array_merge($this->objTemplate->getElements($strTemplateID, 0), $this->objTemplate->getElements($strTemplateID, 1));

        $arrPlaceholders = array();
        //and retransform
        foreach ($arrRawPlaceholders as $arrOneRawPlaceholder)
            $arrPlaceholders[] = $arrOneRawPlaceholder["placeholder"];


        //Initialize the caches internal cache :)
        class_cache::fillInternalCache("class_element_portal", $this->getPagename(), null, $this->getStrPortalLanguage());


        //try to load the additional title from cache
        $strAdditionalTitleFromCache = "";
        $intMaxCacheDuration = 0;
        $objCachedTitle = class_cache::getCachedEntry(__CLASS__, $this->getPagename(), $this->generateHash2Sum(), $this->getStrPortalLanguage());
        if($objCachedTitle != null) {
            $strAdditionalTitleFromCache = $objCachedTitle->getStrContent();
            self::$strAdditionalTitle = $strAdditionalTitleFromCache;
        }


        //copy for the portaleditor
        $arrPlaceholdersFilled = array();

		//Iterate over all elements and pass control to them
		//Get back the filled element
		//Build the array to fill the template
		$arrTemplate = array();

        /** @var class_module_pages_pageelement $objOneElementOnPage */
		foreach($arrElementsOnPage as $objOneElementOnPage) {
			//element really available on the template?
			if(!in_array($objOneElementOnPage->getStrPlaceholder(), $arrPlaceholders)) {
				//next one, plz
				continue;
			}
            else {
                //create a protocol of placeholders filled
                //remove from pe-additional-array, pe code is injected by element directly
                $arrPlaceholdersFilled[] = array("placeholder" => $objOneElementOnPage->getStrPlaceholder(),
                                                        "name" => $objOneElementOnPage->getStrName(),
                                                     "element" => $objOneElementOnPage->getStrElement(),
                                                  "repeatable" => $objOneElementOnPage->getIntRepeat()
                                              );
            }

			//Build the class-name for the object
			$strClassname = uniSubstr($objOneElementOnPage->getStrClassPortal(), 0, -4);
            /** @var  class_element_portal $objElement  */
			$objElement = new $strClassname($objOneElementOnPage);
			//let the element do the work and earn the output
			if(!isset($arrTemplate[$objOneElementOnPage->getStrPlaceholder()]))
				$arrTemplate[$objOneElementOnPage->getStrPlaceholder()] = "";


            //cache-handling. load element from cache.
            //if the element is re-generated, save it back to cache.
            if(_pages_cacheenabled_ == "true" && $this->getParam("preview") != "1" && !$bitErrorpage) {
                $strElementOutput = "";
                //if the portaleditor is disabled, do the regular cache lookups in storage. otherwise regenerate again and again :)
                if($bitPeRequested) {
                    //pe is enabled --> regenerate the funky contents
                    if($objElement->getStatus() == 0) {
                        $arrPeElement = array();
                        $arrPeElement["title"] = $this->getLang("pe_inactiveElement", "pages"). " (".$objOneElementOnPage->getStrElement().")";
                        $strElementOutput = $this->objToolkit->getPeInactiveElement($arrPeElement);
                        $strElementOutput = class_element_portal::addPortalEditorSetActiveCode($strElementOutput, $objElement->getSystemid(), array());
                    }
                    else
                        $strElementOutput = $objElement->getElementOutput();
                }
                else {
                    //pe not to be taken into account --> full support of caching
                    $strElementOutput = $objElement->getElementOutputFromCache();

                    if($objOneElementOnPage->getIntCachetime() > $intMaxCacheDuration)
                        $intMaxCacheDuration = $objOneElementOnPage->getIntCachetime();

                    if($strElementOutput === false) {
                        $strElementOutput = $objElement->getElementOutput();
                        $objElement->saveElementToCache($strElementOutput);
                    }
                }

            }
            else
     			$strElementOutput = $objElement->getElementOutput();


			//any string to highlight?
    		if($this->getParam("highlight") != "") {
    		    $strHighlight = uniStrtolower($this->getParam("highlight"));
    		    //search for matches, but exclude tags
    		    $strElementOutput = preg_replace("#(?!<.*)(?<!\w)(".$strHighlight.")(?!\w|[^<>]*>)#i", "<span class=\"searchHighlight\">$1</span>", $strElementOutput);
    		}

			$arrTemplate[$objOneElementOnPage->getStrPlaceholder()] .= $strElementOutput;
		}

        //pe-code to add new elements on unfilled placeholders --> only if pe is visible?
        if( $bitPeRequested ) {
            //loop placeholders on template in order to remove already filled ones not being repeatable
            $arrRawPlaceholdersForPe = $arrRawPlaceholders;
            foreach($arrPlaceholdersFilled as $arrOnePlaceholder) {

                foreach($arrRawPlaceholdersForPe as &$arrOneRawPlaceholder) {
                    if($arrOneRawPlaceholder["placeholder"] == $arrOnePlaceholder["placeholder"]) {

                        foreach($arrOneRawPlaceholder["elementlist"] as $intElementKey => $arrOneRawElement) {

                            if($arrOnePlaceholder["element"] == $arrOneRawElement["element"]) {
                                if(uniSubstr($arrOneRawElement["name"], 0, 5) == "master") {
                                    $arrOneRawPlaceholder["elementlist"][$intElementKey] = null;
                                }
                                else if($arrOnePlaceholder["repeatable"] == "0") {
                                    $arrOneRawPlaceholder["elementlist"][$intElementKey] = null;
                                }
                            }

                        }

                    }
                }
            }

            //array is now set up. loop again to create new-buttons
            $arrPePlaceholdersDone = array();
            $arrPeNewButtons = array();
            foreach($arrRawPlaceholdersForPe as $arrOneRawPlaceholderForPe) {
                $strPeNewPlaceholder = $arrOneRawPlaceholderForPe["placeholder"];
                foreach($arrOneRawPlaceholderForPe["elementlist"] as $arrOnePeNewElement) {
                    if($arrOnePeNewElement != null) {

                        //check if the linked element exists
                        $objPeNewElement = class_module_pages_element::getElement($arrOnePeNewElement["element"]);
                        if($objPeNewElement != null) {
                            //placeholder processed before?
                            $strArrayKey = $strPeNewPlaceholder.$objPeNewElement->getStrName();

                            if(in_array($strArrayKey, $arrPePlaceholdersDone))
                                continue;
                            else
                                $arrPePlaceholdersDone[] = $strArrayKey;

                            //create and register the button to add a new element
                            if(!isset($arrPeNewButtons[$strPeNewPlaceholder]))
                                $arrPeNewButtons[$strPeNewPlaceholder] = "";

                            $strElementReadableName = $objPeNewElement->getStrDisplayName() != $objPeNewElement->getStrName() ? ($objPeNewElement->getStrDisplayName()." (".$objPeNewElement->getStrName().")") : $objPeNewElement->getStrName();
                            if(uniStripos($strArrayKey, "master") !== false)
                                $strLink = class_element_portal::getPortaleditorNewCode($objMasterData->getSystemid(), $strPeNewPlaceholder, $objPeNewElement->getStrName(), $strElementReadableName);
                            else
                                $strLink = class_element_portal::getPortaleditorNewCode($objPageData->getSystemid(), $strPeNewPlaceholder, $objPeNewElement->getStrName(), $strElementReadableName);

                            $arrPeNewButtons[$strPeNewPlaceholder] .= $strLink;


                        }
                    }
                }
            }

            //loop pe-new code in order to add the wrappers and assign the code to the matching placeholder
            foreach($arrPeNewButtons as $strPlaceholderName => $strNewButtons) {

                if(!isset($arrTemplate[$strPlaceholderName]))
                    $arrTemplate[$strPlaceholderName] = "";

                if($strNewButtons != "")
                    $strNewButtons = class_element_portal::getPortaleditorNewWrapperCode($strPlaceholderName, $strNewButtons);
                $arrTemplate[$strPlaceholderName] .= $strNewButtons;
            }
        }


        //check if the additional title has to be saved to the cache
        if(self::$strAdditionalTitle != "" && self::$strAdditionalTitle != $strAdditionalTitleFromCache) {
            $objCacheEntry = class_cache::getCachedEntry(__CLASS__, $this->getPagename(), $this->generateHash2Sum(), $this->getStrPortalLanguage(), true);
            $objCacheEntry->setStrContent(self::$strAdditionalTitle );
            $objCacheEntry->setIntLeasetime(time()+$intMaxCacheDuration );

            $objCacheEntry->updateObjectToDb();
        }


		$arrTemplate["description"] = $objPageData->getStrDesc();
		$arrTemplate["keywords"] = $objPageData->getStrKeywords();
		$arrTemplate["title"] = $objPageData->getStrBrowsername();
		$arrTemplate["additionalTitle"] = self::$strAdditionalTitle;
		//Include the $arrGlobal Elements
		$arrGlobal = array();
        $strPath = class_resourceloader::getInstance()->getPathForFile("/portal/global_includes.php");
        if($strPath !== false)
		    include(_realpath_.$strPath);

		$arrTemplate = array_merge($arrTemplate, $arrGlobal);
		//fill the template. the template was read before
		$strPageContent = $this->fillTemplate($arrTemplate, $strTemplateID);

        //add the portaleditor toolbar
        if(_pages_portaleditor_ == "true" && ($objPageData->rightEdit()  || $bitEditPermissionOnMasterPage) && $this->objSession->isAdmin()) {

            class_adminskin_helper::defineSkinWebpath();

    		//save back the current portal text language and set the admin-one
    		$strPortalLanguage = class_carrier::getInstance()->getObjLang()->getStrTextLanguage();
    		class_carrier::getInstance()->getObjLang()->setStrTextLanguage($this->objSession->getAdminLanguage());

            if($this->objSession->getSession("pe_disable") != "true" ) {
    		    $strPeToolbar = "";
    		    $arrPeContents = array();
    		    $arrPeContents["pe_status_page"] = $this->getLang("pe_status_page", "pages");
    		    $arrPeContents["pe_status_status"] = $this->getLang("pe_status_status", "pages");
    		    $arrPeContents["pe_status_autor"] = $this->getLang("pe_status_autor", "pages");
    		    $arrPeContents["pe_status_time"] = $this->getLang("pe_status_time", "pages");
                $arrPeContents["pe_status_page_val"] = $objPageData->getStrName();
    		    $arrPeContents["pe_status_status_val"] = ($objPageData->getStatus() == 1 ? "active" : "inactive" );
    		    $arrPeContents["pe_status_autor_val"] = $objPageData->getLastEditUser();
    		    $arrPeContents["pe_status_time_val"] = timeToString($objPageData->getIntLmTime(), false);
    		    $arrPeContents["pe_dialog_close_warning"] = $this->getLang("pe_dialog_close_warning", "pages");

                //Add an iconbar
    		    $arrPeContents["pe_iconbar"] = "";
                //TODO: i18n
                $arrPeContents["pe_iconbar"] .= "<a href=\"#\" onclick=\"KAJONA.admin.portaleditor.savePage(); return false;\" id=\"savePageLink\">".getImageAdmin("icon_acceptDisabled.gif", $this->getLang("Änderungen speichern", "pages"))."</a>";
    		    $arrPeContents["pe_iconbar"] .= "&nbsp;";
    		    $arrPeContents["pe_iconbar"] .= getLinkAdmin("pages_content", "list", "&systemid=".$objPageData->getSystemid()."&language=".$strPortalLanguage, $this->getLang("pe_icon_edit"), $this->getLang("pe_icon_edit", "pages"), "icon_pencil.gif");
    		    $arrPeContents["pe_iconbar"] .= "&nbsp;";

                $strEditUrl = getLinkAdminHref("pages", "editPage", "&systemid=".$objPageData->getSystemid()."&language=".$strPortalLanguage."&pe=1");
                $arrPeContents["pe_iconbar"] .= "<a href=\"#\" onclick=\"KAJONA.admin.portaleditor.openDialog('".$strEditUrl."'); return false;\">".getImageAdmin("icon_page.gif", $this->getLang("pe_icon_page", "pages"))."</a>";

    		    $arrPeContents["pe_iconbar"] .= "&nbsp;";
                $strEditUrl = getLinkAdminHref("pages", "newPage", "&systemid=".$objPageData->getSystemid()."&language=".$strPortalLanguage."&pe=1");
                $arrPeContents["pe_iconbar"] .= "<a href=\"#\" onclick=\"KAJONA.admin.portaleditor.openDialog('".$strEditUrl."'); return false;\">".getImageAdmin("icon_new.gif", $this->getLang("pe_icon_new", "pages"))."</a>";



    		    $arrPeContents["pe_disable"] = "<a href=\"#\" onclick=\"KAJONA.admin.portaleditor.switchEnabled(false); return false;\" title=\"\">".getImageAdmin("icon_enabled.gif", $this->getLang("pe_disable", "pages"))."</a>";

    		    //Load YUI and portaleditor javascript (even if it's maybe already loaded in portal)
    		    $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/yui/yuiloader-dom-event/yuiloader-dom-event.js?"._system_browser_cachebuster_."\"></script>";
    		    $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/kajona_portaleditor.js?"._system_browser_cachebuster_."\"></script>";
                //Load portaleditor styles
                $strPeToolbar .= "\n<script type=\"text/javascript\">KAJONA.admin.loader.loadPortaleditorBase();</script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\">KAJONA.admin.loader.load(null, [\""._skinwebpath_."/styles_portaleditor.css\"]);</script>";
                $strPeToolbar .= "\n<!--[if lt IE 8]><script type=\"text/javascript\">KAJONA.admin.loader.load(null, [\""._skinwebpath_."/styles_portaleditor_ie.css\"]);</script><![endif]-->";
    		    $strPeToolbar .= $this->objToolkit->getPeToolbar($arrPeContents);




//TODO: temporary poc hallo integration, cleanup required


                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/jquery/jquery.min.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/jqueryui/jquery-ui.custom.min.js\"></script>";
                $strPeToolbar .= "\n       <link rel=\"stylesheet\" href=\""._webpath_."/core/module_system/admin/scripts/jqueryui/css/smoothness/jquery-ui.custom.css\" type=\"text/css\">";

                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/jquery/jquery.htmlClean.min.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/rangy/rangy-core.js\"></script>";

                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/hallo-min.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/halloformat.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/headings.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/lists.js\"></script>";
                //$strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/halloeditor/linkimg.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/reundo.js\"></script>";
                $strPeToolbar .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/link.js\"></script>";

                $strPeToolbar .= "<link rel=\"stylesheet\" href=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/hallo.css\" type=\"text/css\">";
                $strPeToolbar .= "<link rel=\"stylesheet\" href=\""._webpath_."/core/module_pages/admin/scripts/halloeditor/fontawesome/css/font-awesome.css\" type=\"text/css\">";
                $strPeToolbar .= <<<CSS
<style id="hintstyles">
    body *[data-kajona-editable] {
        outline: 1px dotted rgba(0, 255, 0, 0.5);
    }
</style>

<style>
    body *[data-kajona-editable] {
        -webkit-transition: all .5s ease-in-out;
        -moz-transition: all .5s ease-in-out;
        /*outline: 2px dotted rgba(0, 255, 0, 0);*/
    }

    body *[data-kajona-editable]:hover {
        outline: 1px dotted rgba(0, 255, 0, 0.5);
        background-color: rgba(0, 255, 0, 0.2);
    }

    .hallotoolbar {
        /*width: 400px !important;*/
    }

    .hallotoolbar .ui-button .ui-button-text {
        /*line-height: auto !important;*/
        font-size: 0.7em !important;
    }

</style>
CSS;

                $strPeToolbar .= "<script type='text/javascript'>";
                $strPeToolbar .= <<<JS


       $(function () {
            KAJONA.admin.portaleditor.RTE = {};
            KAJONA.admin.portaleditor.RTE.modifiedFields = {};

            KAJONA.admin.portaleditor.savePage = function () {

                console.group('savePage');

                $.each(KAJONA.admin.portaleditor.RTE.modifiedFields, function (key, value) {
                    var keySplitted = key.split('#');

                    var data = {
                        systemid: keySplitted[0],
                        property: keySplitted[1],
                        value: value
                    };

                    $.post(KAJONA_WEBPATH + '/xml.php?admin=1&module=pages_content&action=updateObjectProperty', data, function () {
                        console.warn('server response');
                        console.log(this.responseText);
                    });
                });
                console.groupEnd('savePage');
                $('#savePageLink > img').attr('src', $('#savePageLink > img').attr('src').replace(".gif", "Disabled.gif"));
                KAJONA.admin.portaleditor.RTE.modifiedFields = {};
            };


            var pasteHandler = function (event) {
                //disable resizing handles in FF
                document.execCommand("enableObjectResizing", false, false);

                var editable = $(event.currentTarget);

                //find the current cursor-position before creating the paste-container, used lateron
                var sel = rangy.getSelection();

                var offset = editable.offset();
                $('body').append('<div id="pasteContainer" contentEditable="true" style="position:absolute; clip:rect(0px, 0px, 0px, 0px); width: 1px; height: 1px; top: ' + offset.top + 'px; left: ' + offset.left + 'px;"></div>');
                var pasteContainer = $('#pasteContainer');

                var keySplitted = editable.attr('data-kajona-editable').split('#');
                var isPlaintext = (keySplitted[2] && keySplitted[2] == 'plain') ? true : false;
                if (isPlaintext) {
                    var htmlCleanConfig = {
                        allowedTags: ['']
                    };
                } else {
                    var htmlCleanConfig = {
                        allowedTags: ['br', 'p', 'ul', 'ol', 'li']
                    };
                }

                editable.blur();
                pasteContainer.focus();

                window.setTimeout(function() {
                    event.stopPropagation();

                    var content = pasteContainer.html();
                    var cleanContent = $.htmlClean.trim($.htmlClean(content, htmlCleanConfig));
                    console.warn('paste val: ', content, cleanContent);
                    pasteContainer.html('');
                    pasteContainer.remove();

                    //enable resizing handles in FF again
                    document.execCommand("enableObjectResizing", false, true);
                    editable.focus();

                    //update the old selection
                    var strOldHtml = sel.anchorNode.data;
                    var strNewHtml = strOldHtml.substr(0, sel.anchorOffset)+cleanContent+strOldHtml.substring(sel.focusOffset);
                    sel.anchorNode.data = strNewHtml;

                    //editable.html(strNewHtml); //TODO: find matching cursor position, e.g. with http://code.google.com/p/rangy/ -> implemented, see above
                }, 10);
            };



            //loop over all editables
            $('*[data-kajona-editable]').each(function () {
                var editable = $(this);
                var keySplitted = editable.attr('data-kajona-editable').split('#');
                var isPlaintext = (keySplitted[2] && keySplitted[2] == 'plain') ? true : false;

                //attach paste handler
                editable.bind('paste', pasteHandler);

                //prevent enter key when editable is a plaintext field
                if (isPlaintext) {
                    editable.keypress(function(event) {
                        if (event.which == 13) {
                            return false;
                        }
                    });
                }

                //always disable drag&drop
                editable.bind('drop drag', function () {
                    return false;
                });


                //generate hallo editor config
                var halloConfig = {
                    plugins: {
                        halloreundo: {}
                    },
                    modified: function (event, obj) {
                        var attr = $(this).attr('data-kajona-editable');

                        $('#savePageLink > img').attr('src', $('#savePageLink > img').attr('src').replace("Disabled", ""));
                        KAJONA.admin.portaleditor.RTE.modifiedFields[attr] = obj.content;
                        //console.log('modified field', attr, obj.content);
                    }
                };

                if (!isPlaintext) {
                    halloConfig.plugins = {
                        halloformat: {},
                        hallolists: {},
                        halloreundo: {},
                        hallolink: {}

                    };
                }

                //finally init hallo editor
                editable.hallo(halloConfig);
            });

       });

JS;
$strPeToolbar .= "</script>";

//TODO: cleanup end

                //The toolbar has to be added right after the body-tag - to generate correct html-code
    		    $strTemp = uniSubstr($strPageContent, uniStrpos($strPageContent, "<body"));
    		    //find closing bracket
    		    $intTemp = uniStrpos($strTemp, ">")+1;
    		    //and insert the code
    		    $strPageContent = uniSubstr($strPageContent, 0, uniStrpos($strPageContent, "<body")+$intTemp) .$strPeToolbar.uniSubstr($strPageContent, uniStrpos($strPageContent, "<body")+$intTemp) ;
            }
            else {
                //Button to enable the toolbar & pe
                $strEnableButton = "<div id=\"peEnableButton\"><a href=\"#\" onclick=\"KAJONA.admin.portaleditor.switchEnabled(true); return false;\" title=\"\">".getImageAdmin("icon_disabled.gif", $this->getLang("pe_enable", "pages"))."</a></div>";
    		    //Load YUI and portaleditor javascript
    		    $strEnableButton .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/yui/yuiloader-dom-event/yuiloader-dom-event.js?"._system_browser_cachebuster_."\"></script>";
    		    $strEnableButton .= "\n<script type=\"text/javascript\" src=\""._webpath_."/core/module_system/admin/scripts/kajona_portaleditor.js?"._system_browser_cachebuster_."\"></script>";
                //Load portaleditor styles
                $strEnableButton .= "\n<script type=\"text/javascript\">KAJONA.admin.loader.load(null, [\""._skinwebpath_."/styles_portaleditor.css\"]);</script>";
                $strEnableButton .= "\n<!--[if lt IE 8]><script type=\"text/javascript\">KAJONA.admin.loader.load(null, [\""._skinwebpath_."/styles_portaleditor_ie.css\"]);</script><![endif]-->";
                //The toobar has to be added right after the body-tag - to generate correct html-code
    		    $strTemp = uniSubstr($strPageContent, uniStripos($strPageContent, "<body"));
    		    //find closing bracket
    		    $intTemp = uniStripos($strTemp, ">")+1;
    		    //and insert the code
    		    $strPageContent = uniSubstr($strPageContent, 0, uniStrpos($strPageContent, "<body")+$intTemp) .$strEnableButton.uniSubstr($strPageContent, uniStrpos($strPageContent, "<body")+$intTemp) ;
            }

            //reset the portal texts language
            class_carrier::getInstance()->getObjLang()->setStrTextLanguage($strPortalLanguage);
        }

        //insert the copyright headers. Due to our licence, you are NOT allowed to remove those lines.
        $strHeader  = "<!--\n";
        $strHeader .= "Website powered by Kajona³ Open Source Content Management Framework\n";
        $strHeader .= "For more information about Kajona see http://www.kajona.de\n";
        $strHeader .= "-->\n";

        $intBodyPos = uniStripos($strPageContent, "</head>");
        $intPosXml = uniStripos($strPageContent, "<?xml");
        if($intBodyPos !== false) {
            $intBodyPos += 0;
            $strPageContent = uniSubstr($strPageContent, 0, $intBodyPos).$strHeader.uniSubstr($strPageContent, $intBodyPos);
        }
        else if($intPosXml !== false) {
            $intBodyPos = uniStripos($strPageContent, "?>");
            $intBodyPos += 2;
            $strPageContent = uniSubstr($strPageContent, 0, $intBodyPos).$strHeader.uniSubstr($strPageContent, $intBodyPos);
        }
        else {
            $strPageContent = $strHeader.$strPageContent;
        }

		return $strPageContent;
	}

	/**
	 * Sets the passed text as an additional title information.
	 * If set, the separator placeholder from global_includes.php will be included, too.
     * Modules may register additional page-titles in order to have them places as the current page-title.
     * Since this is a single field, the last module wins in case of multiple entries.
     *
	 * @param string $strTitle
	 * @return void
	 */
	public static function registerAdditionalTitle($strTitle) {
		self::$strAdditionalTitle = $strTitle."%%kajonaTitleSeparator%%";
	}

    private function generateHash2Sum() {
        $strGuestId = "";
        //when browsing the site as a guest, drop the userid
        if($this->objSession->isLoggedin())
            $strGuestId = $this->objSession->getUserID();

        return sha1("".$strGuestId.$this->getAction().$this->getParam("pv").$this->getSystemid().$this->getParam("systemid").$this->getParam("highlight") );
    }



}
