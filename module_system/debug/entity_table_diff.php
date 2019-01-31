<?php

$connection = \Kajona\System\System\Database::getInstance();
$orm = new \Kajona\System\System\OrmSchemamanager();
$allTables = [];
$classes = getAllEntities();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($classes as $class) {
        $orm->updateTable($class);
    }
} else {
    foreach ($classes as $class) {
        $tables = $orm->getTableDefinitions($class);
        foreach ($tables as $table) {
            /** @var \Kajona\System\System\OrmSchemamanagerTable $table */
            if ($connection->hasTable($table->getStrName())) {
                foreach ($table->getArrRows() as $column) {
                    if (!$connection->hasColumn($table->getStrName(), $column->getStrName())) {
                        echo "<b>Column {$column->getStrName()} does not exist on table {$table->getStrName()}</b>\n";
                    }
                }
            } else {
                echo "<b>Table {$table->getStrName()} does not exist</b>\n";
            }

            $allTables[] = $table->getStrName();
        }
    }

    $allTables = array_unique($allTables);

    echo "\n";
    echo "Found " . count($classes) . " entities and " . count($allTables) . " tables\n";
}

echo "\n";
echo "<form method='POST'>";
echo "<input type='submit' value='Update Tables'>";
echo "</form>";

function getAllEntities() {
    $filter = function (&$strOneFile, $strPath) {
        $instance = \Kajona\System\System\Classloader::getInstance()->getInstanceFromFilename($strPath, \Kajona\System\System\Root::class);
        if ($instance instanceof \Kajona\System\System\Root) {
            $strOneFile = get_class($instance);
        } else {
            $strOneFile = null;
        }
    };

    $classes = \Kajona\System\System\Resourceloader::getInstance()->getFolderContent("/system", array(".php"), false, null, $filter);
    $classes = array_filter($classes);

    return $classes;
}