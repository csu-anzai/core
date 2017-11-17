<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                                  *
********************************************************************************************************/

/*
    Config-file for the ldap-connector. 
    The sample-file is created to match the structure of an ms active directory.

    There may be configured multiple ldap sources, each identified by the numerical array key.
    Do not change the key as soon as the provider os being used, otherwise mapped users and groups may be wrong.
*/


$config = array();

$config["tika_exec"] = "java -jar " . __DIR__ . "/../../../../bin/tika-app-1.16.jar";
