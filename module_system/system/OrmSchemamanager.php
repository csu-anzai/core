<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                        *
********************************************************************************************************/

namespace Kajona\System\System;

/**
 * The schemamanager-class is used to generate the table out of an objects annotations.
 *
 * As per Kajona 4.6, only the initial create table is supported.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.6
 */
class OrmSchemamanager extends OrmBase
{
    private static $arrColumnDataTypes = array(
        DbDatatypes::STR_TYPE_INT,
        DbDatatypes::STR_TYPE_LONG,
        DbDatatypes::STR_TYPE_DOUBLE,
        DbDatatypes::STR_TYPE_CHAR10,
        DbDatatypes::STR_TYPE_CHAR20,
        DbDatatypes::STR_TYPE_CHAR100,
        DbDatatypes::STR_TYPE_CHAR254,
        DbDatatypes::STR_TYPE_CHAR500,
        DbDatatypes::STR_TYPE_TEXT,
        DbDatatypes::STR_TYPE_LONGTEXT
    );

    /**
     * Creates all tables associated with the model class
     *
     * @param string $strClass
     * @throws Exception
     * @throws OrmException
     */
    public function createTable($strClass)
    {
        $this->setObjObject($strClass);
        if (!$this->hasTargetTable()) {
            throw new OrmException("Class ".$strClass." provides no target-table!", OrmException::$level_ERROR);
        }

        $connection = Database::getInstance();
        $definitions = $this->collectTableDefinitions($strClass);
        $definitions = array_merge($definitions, $this->collectAssignmentDefinitions($strClass));

        foreach ($definitions as $table) {
            if (!$connection->hasTable($table->getStrName())) {
                $this->createTableSchema($table);
            }
        }
    }

    /**
     * Checks all table definitions of the model class and updates the missing columns on each table
     *
     * @param string $strClass
     * @throws Exception
     * @throws OrmException
     */
    public function updateTable($strClass)
    {
        $connection = Database::getInstance();
        $definitions = $this->collectTableDefinitions($strClass);
        $definitions = array_merge($definitions, $this->collectAssignmentDefinitions($strClass));

        foreach ($definitions as $table) {
            if ($connection->hasTable($table->getStrName())) {
                $this->updateTableSchema($table);
            } else {
                $this->createTableSchema($table);
            }
        }
    }

    /**
     * @param OrmSchemamanagerTable $table
     * @throws Exception
     * @throws OrmException
     */
    private function createTableSchema(OrmSchemamanagerTable $table)
    {
        $connection = Carrier::getInstance()->getObjDB();
        $index = array();
        $primary = array();
        $fields = array();

        /** @var OrmSchemamanagerRow $column */
        foreach ($table->getArrRows() as $column) {
            $fields[$column->getStrName()] = array($column->getStrDatatype(), $column->getBitNull());

            if ($column->getBitPrimaryKey()) {
                $primary[] = $column->getStrName();
            }

            if ($column->getBitIndex()) {
                $index[] = $column->getStrName();
            }
        }

        $return = $connection->createTable($table->getStrName(), $fields, $primary, $index);
        if (!$return) {
            throw new OrmException("Error creating table " . $table->getStrName());
        }
    }

    /**
     * @param OrmSchemamanagerTable $table
     * @throws OrmException
     */
    private function updateTableSchema(OrmSchemamanagerTable $table)
    {
        $connection = Carrier::getInstance()->getObjDB();

        /** @var OrmSchemamanagerTable $table */
        foreach ($table->getArrRows() as $column) {
            /** @var OrmSchemamanagerRow $column */
            if (!$connection->hasColumn($table->getStrName(), $column->getStrName())) {
                $return = $connection->addColumn($table->getStrName(), $column->getStrName(), $column->getStrDatatype(), $column->getBitNull());
                if (!$return) {
                    throw new OrmException("Could not add column " . $column->getStrName() . " on table " . $table->getStrName());
                }
            } else {
                // @TODO maybe change column type
            }
        }
    }

    /**
     * @param string $strClass
     *
     * @return array
     * @throws OrmException
     * @throws Exception
     */
    private function collectTableDefinitions($strClass)
    {
        $objReflection = new Reflection($strClass);

        $arrTargetTables = $objReflection->getAnnotationValuesFromClass(self::STR_ANNOTATION_TARGETTABLE);

        /** @var OrmSchemamanagerTable[] $arrCreateTables */
        $arrCreateTables = array();

        foreach ($arrTargetTables as $strValue) {
            $arrTable = explode(".", $strValue);

            if (count($arrTable) != 2) {
                throw new OrmException("Target table for ".$strClass." is not in table.primaryColumn format", OrmException::$level_ERROR);
            }

            $objTable = new OrmSchemamanagerTable($arrTable[0]);
            $objTable->addRow(new OrmSchemamanagerRow($arrTable[1], DbDatatypes::STR_TYPE_CHAR20, false, true));
            $arrCreateTables[$arrTable[0]] = $objTable;
        }

        //merge them with the list of mapped columns
        $arrProperties = $objReflection->getPropertiesWithAnnotation(self::STR_ANNOTATION_TABLECOLUMN);
        foreach ($arrProperties as $strProperty => $strTableColumn) {
            //fetch the target data-type
            $strTargetDataType = $objReflection->getAnnotationValueForProperty($strProperty, self::STR_ANNOTATION_TABLECOLUMNDATATYPE);
            if ($strTargetDataType == null) {
                $strTargetDataType = DbDatatypes::STR_TYPE_CHAR254;
            }

            if (!in_array($strTargetDataType, self::$arrColumnDataTypes)) {
                throw new OrmException("Datatype ".$strTargetDataType." is unknown (".$strProperty."@".$strClass.")", OrmException::$level_ERROR);
            }

            $arrColumn = explode(".", $strTableColumn);

            if (count($arrColumn) != 2 && count($arrTargetTables) > 1) {
                throw new OrmException("Syntax for tableColumn annotation at property ".$strProperty."@".$strClass." not in format table.columnName", OrmException::$level_ERROR);
            }
            if (count($arrColumn) == 1 && count($arrTargetTables) == 1) {
                //copy the column name, table is the current one
                $arrTable = explode(".", $arrTargetTables[0]);
                $arrColumn[1] = $arrColumn[0];
                $arrColumn[0] = $arrTable[0];
            }


            $objRow = new OrmSchemamanagerRow($arrColumn[1], $strTargetDataType);

            if ($objReflection->hasPropertyAnnotation($strProperty, OrmBase::STR_ANNOTATION_TABLECOLUMNINDEX)) {
                $objRow->setBitIndex(true);
            }

            if ($objReflection->hasPropertyAnnotation($strProperty, OrmBase::STR_ANNOTATION_TABLECOLUMNPRIMARYKEY)) {
                $objRow->setBitPrimaryKey(true);
            }

            if (isset($arrCreateTables[$arrColumn[0]])) {
                $objTable = $arrCreateTables[$arrColumn[0]];
                $objTable->addRow($objRow);
            }
        }

        return $arrCreateTables;
    }


    /**
     * Processes all object assignments in order to generate the relevant tables
     *
     * @param $strClass
     *
     * @return array
     * @throws Exception
     */
    private function collectAssignmentDefinitions($strClass)
    {

        $arrAssignmentTables = array();
        $objReflection = new Reflection($strClass);

        //get the mapped properties
        $arrProperties = $objReflection->getPropertiesWithAnnotation(OrmBase::STR_ANNOTATION_OBJECTLIST, ReflectionEnum::PARAMS);

        foreach ($arrProperties as $strPropertyName => $arrValues) {
            $strTableName = $objReflection->getAnnotationValueForProperty($strPropertyName, OrmBase::STR_ANNOTATION_OBJECTLIST);

            if (!isset($arrValues["source"]) || !isset($arrValues["target"]) || empty($strTableName)) {
                continue;
            }

            $objTable = new OrmSchemamanagerTable($strTableName);
            $objTable->addRow(new OrmSchemamanagerRow($arrValues["source"], DbDatatypes::STR_TYPE_CHAR20, false, true));
            $objTable->addRow(new OrmSchemamanagerRow($arrValues["target"], DbDatatypes::STR_TYPE_CHAR20, false, true));

            $arrAssignmentTables[] = $objTable;
        }

        return $arrAssignmentTables;
    }

}
