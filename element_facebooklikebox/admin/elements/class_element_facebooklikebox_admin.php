<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/


/**
 * Class to handle the admin-stuff of the tagto-element
 *
 * @package element_facebooklikebox
 * @author jschroeter@kajona.de
 *
 * @targetTable element_universal.content_id
 *
 */
class class_element_facebooklikebox_admin extends class_element_admin implements interface_admin_element {


    /**
     * @var string
     * @tableColumn element_universal.char1
     *
     * @fieldType template
     * @fieldLabel template
     *
     * @fieldTemplateDir /element_facebooklikebox
     */
    private $strChar1;



    /**
     * @param string $strChar1
     */
    public function setStrChar1($strChar1) {
        $this->strChar1 = $strChar1;
    }

    /**
     * @return string
     */
    public function getStrChar1() {
        return $this->strChar1;
    }



}