<?php


// MUSS IN SYNC MIT INSTALLER SYSTEM SEIN!

use Kajona\System\System\DbDatatypes;


echo "Creating group map".PHP_EOL;
$groupMap = [];
foreach (\Kajona\System\System\Database::getInstance()->getPArray("SELECT group_id, group_short_id FROM agp_user_group", []) as $row) {
    $groupMap[$row["group_short_id"]] = $row["group_id"];
}


echo "Creating agp_permissions_view permissions table".PHP_EOL;
\Kajona\System\System\Database::getInstance()->createTable(
    "agp_permissions_view",
    [
        "view_id" => [DbDatatypes::STR_TYPE_CHAR20, false],
        "view_group" => [DbDatatypes::STR_TYPE_CHAR20, false],
        "view_shortgroup" => [DbDatatypes::STR_TYPE_LONG, false],
    ],
    ["view_id", "view_group", "view_shortgroup"],
    [["view_id", "view_shortgroup"], ["view_id", "view_group"], "view_id", "view_group", "view_shortgroup"]
);

\Kajona\System\System\Database::getInstance()->_pQuery("TRUNCATE agp_permissions_view", []);

$insert = [];
foreach (\Kajona\System\System\Database::getInstance()->getPArray("SELECT system_id, right_view FROM agp_system", []) as $i => $row) {

    $groups = explode(",", trim($row["right_view"], ','));

    foreach ($groups as $shortid) {
        if (is_numeric($shortid) && array_key_exists($shortid, $groupMap)) {
            $insert[] = [$row['system_id'], $groupMap[$shortid], $shortid];
        }
    }


    if ($i % 100 == 0) {
        if (!empty($insert)) {
            \Kajona\System\System\Database::getInstance()->multiInsert("agp_permissions_view", ["view_id", "view_group", "view_shortgroup"], $insert);
            $insert = [];
        }
        echo "Migrated {$i} records ".PHP_EOL;
        ob_flush();
        flush();
    }
}

if (!empty($insert)) {
    \Kajona\System\System\Database::getInstance()->multiInsert("agp_permissions_view", ["view_id", "view_group", "view_shortgroup"], $insert);
    $insert = [];
}
echo "Migrated {$i} records ".PHP_EOL;
ob_flush();
flush();


echo "Creating agp_permissions_right2 permissions table".PHP_EOL;
\Kajona\System\System\Database::getInstance()->createTable(
    "agp_permissions_right2",
    [
        "right2_id" => [DbDatatypes::STR_TYPE_CHAR20, false],
        "right2_group" => [DbDatatypes::STR_TYPE_CHAR20, false],
        "right2_shortgroup" => [DbDatatypes::STR_TYPE_LONG, false],
    ],
    ["right2_id", "right2_group", "right2_shortgroup"],
    [["right2_id", "right2_shortgroup"], ["right2_id", "right2_group"], "right2_id", "right2_group", "right2_shortgroup"]
);

\Kajona\System\System\Database::getInstance()->_pQuery("TRUNCATE agp_permissions_right2", []);

$insert = [];
foreach (\Kajona\System\System\Database::getInstance()->getPArray("SELECT system_id, right_right2 FROM agp_system", []) as $i => $row) {

    $groups = explode(",", trim($row["right_right2"], ','));

    foreach ($groups as $shortid) {
        if (is_numeric($shortid) && array_key_exists($shortid, $groupMap)) {
            $insert[] = [$row['system_id'], $groupMap[$shortid], $shortid];
        }
    }


    if ($i % 100 == 0) {
        if (!empty($insert)) {
            \Kajona\System\System\Database::getInstance()->multiInsert("agp_permissions_right2", ["right2_id", "right2_group", "right2_shortgroup"], $insert);
            $insert = [];
        }
        echo "Migrated {$i} records ".PHP_EOL;
        ob_flush();
        flush();
    }
}

if (!empty($insert)) {
    \Kajona\System\System\Database::getInstance()->multiInsert("agp_permissions_right2", ["right2_id", "right2_group", "right2_shortgroup"], $insert);
    $insert = [];
}
echo "Migrated {$i} records ".PHP_EOL;
ob_flush();
flush();


