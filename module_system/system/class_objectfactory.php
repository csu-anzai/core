<?php
/*"******************************************************************************************************
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id: class_filesystem.php 4325 2011-12-02 13:22:25Z sidler $                                        *
********************************************************************************************************/

/**
 * The objectfactory is a central place to create instances of common objects.
 * Therefore, a systemid is passed and the system returns the matching business object.
 *
 * Instantiations are cached, so recreating instances is a rather cheap operation.
 * To ensure a proper caching, the factory itself reflects the singleton pattern.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.0
 */
class class_objectfactory {

    /**
     * @var class_model[]
     */
    private $arrObjectCache = array();

    /**
     * @var #M#M#C\class_carrier.getInstance.getObjDB|?
     */
    private $objDB;

    /**
     * @var class_objectfactory
     */
    private static $objInstance = null;

    private function __construct() {
        $this->objDB = class_carrier::getInstance()->getObjDB();
    }

    private function __clone() {
    }

    /**
     * Returns an instance of the objectfactory.
     *
     * @static
     * @return class_objectfactory
     */
    public static function getInstance() {
        if(self::$objInstance == null)
            self::$objInstance = new class_objectfactory();

        return self::$objInstance;
    }


    /**
     * Creates a new object-instance. Therefore, the passed system-id
     * is searched in the cache, afterwards the instance is created - as long
     * as the matching class could be found, otherwise null
     *
     * @param string $strSystemid
     * @param bool $bitIgnoreCache
     * @return null|class_model|interface_model
     */
    public function getObject($strSystemid, $bitIgnoreCache = false) {

        if(!$bitIgnoreCache && isset($this->arrObjectCache[$strSystemid]))
            return $this->arrObjectCache[$strSystemid];

        //load the object itself
        $strQuery = "SELECT * FROM "._dbprefix_."system where system_id = ?";
        $arrRow = $this->objDB->getPRow($strQuery, array($strSystemid));
        if(isset($arrRow["system_class"])) {
            $objReflection = new ReflectionClass($arrRow["system_class"]);

            $objObject = $objReflection->newInstance($strSystemid);

            $this->arrObjectCache[$strSystemid] = $objObject;

            return $objObject;
        }

        return null;
    }

}

