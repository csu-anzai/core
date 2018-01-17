<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Dbdump\System;

use Kajona\System\System\Config;
use Kajona\System\System\Database;
use Kajona\System\System\Date;
use Kajona\System\System\Filesystem;
use Kajona\System\System\SystemModule;
use Kajona\System\System\Zip;

/**
 * Creates a file-based backup of the current database tables.
 * A file is created per table, the data itself is streamed.
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 */
class DbExport
{

    const LINE_SEPARATOR = "§%§%§\n";
    const MARKER_FILE = "export.json";

    /**
     * @var Database
     */
    private $objDB;

    /**
     * @var
     */
    private $bitPrintDebug;

    /**
     * @var string[]
     */
    private $arrExcludedTables = [];

    /**
     * DbExport constructor.
     * @param Database $objDB
     * @param bool $bitPrintDebug
     */
    public function __construct(Database $objDB, $arrExcludedTables = [], $bitPrintDebug = false)
    {
        $this->objDB = $objDB;
        $this->bitPrintDebug = $bitPrintDebug;
        $this->arrExcludedTables = $arrExcludedTables;
    }

    /**
     * Exports the current database to a single zip-file
     * @throws \Kajona\System\System\Exception
     */
    public function createExport(): bool
    {
        $strTarget = "/project/temp/dbexport_".generateSystemid();

        $objFilesystem = new Filesystem();
        if (!$objFilesystem->folderCreate($strTarget)) {
            return false;
        }

        //create a marker file
        $arrVersions = [];
        foreach (SystemModule::getAllModules() as $objModule) {
            $arrVersions[$objModule->getStrName()] = $objModule->getStrVersion();
        }
        file_put_contents(_realpath_.$strTarget."/".self::MARKER_FILE, json_encode([
            "date" => Date::getCurrentTimestamp(),
            "driver" => Config::getInstance()->getConfig("dbdriver"),
            "modules" => $arrVersions
        ]));

        $bitReturn = true;
        foreach ($this->objDB->getTables() as $strTable) {
            if (in_array($strTable, $this->arrExcludedTables)) {
                continue;
            }
            if (!$this->exportTable($strTable, $strTarget)) {
                $bitReturn = false;
                break;
            }
        }

        if (!$this->zipTargetDir($strTarget, "/project/dbdumps/dbdump_kj_".time().".zip")) {
            $bitReturn = false;
        }

        //clean in every case
        if (!$objFilesystem->folderDeleteRecursive($strTarget)) {
            $bitReturn = false;
        }

        return $bitReturn;
    }

    /**
     * Creates a zip archive of a single folder
     * @param $strSourceDir
     * @param $strTargetFilename
     * @return bool
     */
    private function zipTargetDir(string $strSourceDir, string $strTargetFilename): bool
    {
        $objZip = new Zip();
        $objZip->openArchiveForWriting($strTargetFilename);

        if (is_dir(_realpath_.$strSourceDir)) {
            $objFilesystem = new Filesystem();
            $arrFiles = $objFilesystem->getCompleteList($strSourceDir, array(), array(), array(".", ".."));
            foreach ($arrFiles["files"] as $arrOneFile) {
                if (!$objZip->addFile($arrOneFile["filepath"], basename($arrOneFile["filepath"]))) {
                    return false;
                }
            }
        }

        $objZip->closeArchive();
        return true;
    }

    /**
     * Exports a single table into a single file
     *
     * @param $strTable
     * @param $strTargetDir
     * @return bool
     */
    private function exportTable(string $strTable, string $strTargetDir): bool
    {
        //fetch the columns in order to get a sort-col
        $arrColumns = $this->objDB->getColumnsOfTable($strTable);

        $strTargetFile = $strTargetDir."/".$strTable.".ser";

        $objFile = new Filesystem();
        if (!$objFile->openFilePointer($strTargetFile)) {
            return false;
        }

        if ($this->bitPrintDebug) {
            echo "Exporting table ".str_pad($strTable."", 28)." rows: ";
            ob_flush();
            flush();
        }

        $intCount = 0;
        $intPrint = 0;
        //sort by first an second column by default, avoids problems with combined primary keys
        foreach ($this->objDB->getGenerator("SELECT * FROM ".$strTable." ORDER BY ".$arrColumns[0]["columnName"]. " ASC ".(isset($arrColumns[1]) ? ", ".$arrColumns[1]["columnName"]. " ASC" : "")) as $arrRows) {
            foreach ($arrRows as $arrRow) {
                if (!$objFile->writeToFile(serialize($arrRow).self::LINE_SEPARATOR)) {
                    return false;
                }
                $intCount++;

                if ($this->bitPrintDebug) {
                    if ($intCount % 500 == 0) {
                        echo str_pad($intCount . "", 10, " ");

                        if ($intPrint++ > 15) {
                            echo PHP_EOL.str_pad("", 51);
                            $intPrint = 0;
                        }
                        ob_flush();
                        flush();
                    }
                }
            }
        }

        if ($this->bitPrintDebug) {
            echo "{$intCount}".PHP_EOL;
            ob_flush();
            flush();
        }

        $objFile->closeFilePointer();
        return true;
    }

}
