<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                                  *
********************************************************************************************************/

$config = array();

/**
 * Command to execute the Tika (https://tika.apache.org/) jar
 */
$config["tika_exec"] = "java -jar " . __DIR__ . "/../../../../bin/tika-app-1.16.jar";
