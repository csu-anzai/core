<?php 

//database-settings
define("DB_HOST",				                "192.168.60.220");
define("DB_USER",                               "kajonabuild");
define("DB_PASS",                               "kajonabuild");
define("DB_DB",                                 "kajonabuild");
define("DB_DRIVER",                             "sqlsrv");


ini_set("session.save_path", sys_get_temp_dir());
ini_set("session.use_cookies", "Off");
