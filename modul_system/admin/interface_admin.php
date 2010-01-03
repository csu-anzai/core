<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2010 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                          *
********************************************************************************************************/

/**
 * Interface for all admin-classes (modules)
 * Ensures, that all needed methods are being implemented
 *
 * @package modul_system
 */
interface interface_admin {
	/**
	 * This method is being called from index.php and controls all other actions
	 * If given, the action passed in the GET-Array is being passed by param
	 *
	 * @param string $strAction
	 */
	public function action($strAction = "");

	/**
	 * This method writes the output, generated by the module, to the output Arrays
	 *
	 */
	public function getOutputContent();

}
?>