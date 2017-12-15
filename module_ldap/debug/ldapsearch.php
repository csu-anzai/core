<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                     *
********************************************************************************************************/

use Kajona\Ldap\System\Ldap;
use Kajona\System\System\Config;

echo "+-------------------------------------------------------------------------------+\n";
echo "| Kajona Debug Subsystem                                                        |\n";
echo "|                                                                               |\n";
echo "| ldap search                                                                   |\n";
echo "|                                                                               |\n";
echo "+-------------------------------------------------------------------------------+\n";

$objToolkit = \Kajona\System\System\Carrier::getInstance()->getObjToolkit("admin");
echo "<form method='post'>";
echo "Username: <input type='text' name='username' value='".($_POST["username"] ?? "")."'>";
echo "<input type='submit' name='search_user' value='Search user'>";
echo "</form>";

if (!empty($_POST["search_user"]) && !empty($_POST["username"])) {
    echo "Searching for user ".$_POST["username"].PHP_EOL;

    foreach (Ldap::getAllInstances() as $objLdap) {
        echo "Query to ".$objLdap->getStrCfgName().PHP_EOL;
        $arrUser = $objLdap->getUserdetailsByName($_POST["username"]);
        echo print_r($arrUser, true);
        echo PHP_EOL;
    }

    echo "Querying local database".PHP_EOL;
    $objSources = new \Kajona\System\System\UserSourcefactory();
    $objUser = $objSources->getUserByUsername($_POST["username"]);
    if ($objUser != null) {
        echo "Internal User found".PHP_EOL;
        echo "Subsystem: ".$objUser->getStrSubsystem().PHP_EOL;

    } else {
        echo "User not found in local database".PHP_EOL;
    }


}


echo "\n\n";
echo "+-------------------------------------------------------------------------------+\n";
echo "| (c) www.kajona.de                                                             |\n";
echo "+-------------------------------------------------------------------------------+\n";


