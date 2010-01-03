<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2010 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                               *
********************************************************************************************************/

/**
 * Installer to install a login-element to use in the portal
 *
 * @package modul_pages
 */
class class_installer_element_portallogin extends class_installer_base implements interface_installer {

    /**
     * Constructor
     *
     */
	public function __construct() {
        $arrModule = array();
		$arrModule["version"] 		= "3.2.91";
		$arrModule["name"] 			= "element_portallogin";
		$arrModule["name_lang"] 	= "Element Portallogin";
		$arrModule["nummer2"] 		= _pages_content_modul_id_;
		$arrModule["tabellen"][]    = _dbprefix_."element_portallogin";
		parent::__construct($arrModule);
	}

	public function getNeededModules() {
	    return array("system", "pages");
	}
	
    public function getMinSystemVersion() {
	    return "3.2.0.9";
	}

	public function hasPostInstalls() {
	    //needed: pages
	    try {
		    $objModule = class_modul_system_module::getModuleByName("pages");
		}
		catch (class_exception $objE) {
		    return false;
		}

	    //check, if not already existing
	    $objElement = null;
		try {
		    $objElement = class_modul_pages_element::getElement("portallogin");
		}
		catch (class_exception $objEx)  {
		}
        if($objElement == null)
            return true;

        return false;
	}

    public function hasPostUpdates() {
        $objElement = null;
		try {
		    $objElement = class_modul_pages_element::getElement("portallogin");
            if($objElement != null && version_compare($this->arrModule["version"], $objElement->getStrVersion(), ">"))
                return true;
		}
		catch (class_exception $objEx)  {
		}

        return false;
    }

	public function install() {
    }

    public function postInstall() {
		$strReturn = "";

       	//Table for page-element
		$strReturn .= "Installing formular-element table...\n";
		
		$arrFields = array();
		$arrFields["content_id"] 				= array("char20", false);
		$arrFields["portallogin_template"] 		= array("char254", true);
		$arrFields["portallogin_error"] 		= array("char254", true);
		$arrFields["portallogin_success"] 		= array("char254", true);
		$arrFields["portallogin_logout_success"]= array("char254", true);
        $arrFields["portallogin_profile"]       = array("char254", true);
        $arrFields["portallogin_pwdforgot"]     = array("char254", true);
		
		if(!$this->objDB->createTable("element_portallogin", $arrFields, array("content_id")))
			$strReturn .= "An error occured! ...\n";

		//Register the element
		$strReturn .= "Registering portallogin-element...\n";
		//check, if not already existing
        $objElement = null;
		try {
		    $objElement = class_modul_pages_element::getElement("portallogin");
		}
		catch (class_exception $objEx)  {
		}
		if($objElement == null) {
		    $objElement = new class_modul_pages_element();
		    $objElement->setStrName("portallogin");
		    $objElement->setStrClassAdmin("class_element_portallogin.php");
		    $objElement->setStrClassPortal("class_element_portallogin.php");
		    $objElement->setIntCachetime(-1);
		    $objElement->setIntRepeat(1);
            $objElement->setStrVersion($this->getVersion());
			$objElement->updateObjectToDb();
			$strReturn .= "Element registered...\n";
		}
		else {
			$strReturn .= "Element already installed!...\n";
		}

		return $strReturn;
	}


	public function update() {
	}

    public function postUpdate() {
        $strReturn = "";
        if(class_modul_pages_element::getElement("portallogin")->getStrVersion() == "3.2.0.9") {
            $strReturn .= $this->postUpdate_3209_321();
        }
        $this->objDB->flushQueryCache();

        if(class_modul_pages_element::getElement("portallogin")->getStrVersion() == "3.2.1") {
            $strReturn .= $this->postUpdate_321_3291();
        }

        return $strReturn;
    }

    public function postUpdate_3209_321() {
        $strReturn = "";
        $strReturn = "Updating element portallogin to 3.2.1...\n";
        $this->updateElementVersion("portallogin", "3.2.1");
        return $strReturn;
    }

    public function postUpdate_321_3291() {
        $strReturn = "";
        $strReturn = "Updating element portallogin to 3.2.91...\n";

        $strReturn .= "Updating element table...\n";
        $strSql = "ALTER TABLE ".$this->objDB->encloseTableName(_dbprefix_."element_portallogin")."
                           ADD ".$this->objDB->encloseColumnName("portallogin_pwdforgot")." VARCHAR (254) NULL ";

        if(!$this->objDB->_query($strSql))
            $strReturn .= "An error occured!\n";

        $this->updateElementVersion("portallogin", "3.2.91");
        return $strReturn;
    }
}
?>