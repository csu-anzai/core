<?php 

//database-settings
define("DB_HOST",				                "192.168.60.217");
define("DB_USER",                               "kajona_build");
define("DB_PASS",                               "agp");
define("DB_DB",                                 "");
define("DB_DRIVER",                             "oci8");
//define("DB_DRIVER",                             "mysql");
//define("DB_DRIVER",                             "postgres");




ini_set("session.save_path", sys_get_temp_dir());
ini_set("session.use_cookies", "Off");
