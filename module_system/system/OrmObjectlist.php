<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                        *
********************************************************************************************************/

namespace Kajona\System\System;

use ReflectionClass;


/**
 * The objectlist class is used to load a list of objects or to count a list of objects.
 * Therefore it's not necessary to pass an object instance when creating an instance of OrmObjectlist.
 *
 * Pass the class-name of the queried object-type to either
 *   - getObjectCount()
 *   - getObjectList()
 *   - getSingleObject()
 *
 * By default the generated query has no additional where-restrictions and processes
 * the property marked with @listOrder to sort the result. Nevertheless, the api
 * provides methods to add additional restrictions and sort-orders before calling the
 * getter-methods:
 *   - addOrderBy
 *   - addWhereRestriction
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.6
 * @see OrmObjectlist_restriction
 * @see OrmObjectlist_orderby
 */
class OrmObjectlist extends OrmBase
{

    /**
     * @var OrmCondition[]
     */
    private $arrWhereRestrictions = array();

    /**
     * @var OrmObjectlistOrderby[]
     */
    private $arrOrderBy = array();


    /**
     * Counts the objects found by the currently setup query.
     *
     * @param string|array $strTargetClass
     * @param string $strPrevid
     *
     * @return int
     *
     * @see OrmObjectlist_restriction
     * @see OrmObjectlist_orderby
     */
    public function getObjectCount($strTargetClass, $strPrevid = "")
    {
        $arrParams = array();

        if (is_array($strTargetClass)) {
            $parts = [];
            foreach ($strTargetClass as $targetClass) {
                $parts[] = $this->getQuery($targetClass, $strPrevid, $arrParams, "COUNT(*) AS cnt", false);
            }

            $strQuery = Database::getInstance()->getUnionExpression($parts);
        } else {
            $strQuery = $this->getQuery($strTargetClass, $strPrevid, $arrParams, "COUNT(*) AS cnt", false);
        }

        $results = Carrier::getInstance()->getObjDB()->getPArray($strQuery, $arrParams);

        $count = 0;
        foreach ($results as $row) {
            $count+= (int) $row["cnt"];
        }

        return $count;
    }

    /**
     * Returns the list of object id's matching the current query. The target-tables
     * are set up by analyzing the classes' annotations, the initial sort-order, too.
     * You may influence the ordering and restrictions by adding the relevant restriction / order
     * objects before calling this method.
     *
     * @param string|array $strTargetClass
     * @param string $strPrevid
     * @param null|int $intStart
     * @param null|int $intEnd
     *
     * @return array of system ids
     *
     * @see OrmObjectlist_restriction
     * @see OrmObjectlist_orderby
     */
    public function getObjectListIds($strTargetClass, $strPrevid = "", $intStart = null, $intEnd = null)
    {
        $arrParams = array();

        if (is_array($strTargetClass)) {
            $select = $this->getIntersectSelect($strTargetClass);
            $parts = [];
            foreach ($strTargetClass as $targetClass) {
                $parts[] = $this->getQuery($targetClass, $strPrevid, $arrParams, $select, false);
            }

            $strQuery = Database::getInstance()->getUnionExpression($parts);
            $strQuery.= $this->getUnionOrderBy($strTargetClass);
        } else {
            $strQuery = $this->getQuery($strTargetClass, $strPrevid, $arrParams);
        }

        //$s = Carrier::getInstance()->getObjDB()->prettifyQuery($strQuery, $arrParams);
        $arrRows = Carrier::getInstance()->getObjDB()->getPArray($strQuery, $arrParams, $intStart, $intEnd);

        $arrReturn = array();
        foreach ($arrRows as $arrOneRow) {
            //Caching is only allowed if the fetched and required classes match. Otherwise there could be missing queried tables.

            // @TODO we should move the system_class check into the query which would increase the performance. We have
            // this currently on the PHP side to handle abstract classes etc. But handling abstract classes produces
            // some problems i.e. we need to make for each entry an additional query means we run into the n+1 query
            // problem. Also the count query could have problems since it does not handles such a logic

            if (!is_array($strTargetClass)) {
                if ($arrOneRow["system_class"] == $strTargetClass) {
                    OrmRowcache::addSingleInitRow($arrOneRow);
                    $arrReturn[] = $arrOneRow["system_id"];
                } else {
                    $objReflectionClass = new ReflectionClass($arrOneRow["system_class"]);
                    if ($objReflectionClass->isSubclassOf($strTargetClass)) {
                        //returns the instance, but enforces a fresh reload from the database.
                        //this is useful if extending classes need to query additional tables
                        $arrReturn[] = $arrOneRow["system_id"];
                    }
                }
            } else {
                // in case we want to query multiple entities
                if (in_array($arrOneRow["system_class"], $strTargetClass)) {
                    // NOTE this could be dangerous since $arrOneRow contains not all available columns so we have
                    // later on a partial entity, means an entity where not all fields are available (only the fields
                    // which are available on every entity). But we still add this to the cache since otherwise we run
                    // into the n+1 query problem
                    OrmRowcache::addSingleInitRow($arrOneRow);
                    $arrReturn[] = $arrOneRow["system_id"];
                } else {
                    // for multi types we dont support abstract classes so you have to provide the concrete class, means
                    // the name which is also used in the database
                }
            }
        }

        return $arrReturn;
    }

    /**
     * Returns the list of objects matching the current query. The target-tables
     * are set up by analyzing the classes' annotations, the initial sort-order, too.
     * You may influence the ordering and restrictions by adding the relevant restriction / order
     * objects before calling this method.
     *
     * It is possible to select multiple different entities by providing an array of target classes. In this case we
     * query all intersection tables of the provided classes. It is always preferred to provide only a single entity but
     * in some cases it is required to select multiple different entities so this should be used cautiously
     *
     * @param string|array $strTargetClass
     * @param string $strPrevid
     * @param null|int $intStart
     * @param null|int $intEnd
     *
     * @return Model[]|ModelInterface[]
     *
     * @see OrmObjectlist_restriction
     * @see OrmObjectlist_orderby
     */
    public function getObjectList($strTargetClass, $strPrevid = "", $intStart = null, $intEnd = null)
    {
        $arrIds = $this->getObjectListIds($strTargetClass, $strPrevid, $intStart, $intEnd);

        $arrReturn = array();

        foreach ($arrIds as $strId) {
            $arrReturn[] = Objectfactory::getInstance()->getObject($strId);
        }

        return $arrReturn;
    }

    /**
     * Returns a single object matching the current query. The matching object is either
     * limited by the where statements set up in advance or the first record of the matching
     * result-set is returned.
     * If the query results in an empty result set, null is returned instead.
     *
     * @param string $strTargetClass
     * @param string $strPrevid
     *
     * @return Model|ModelInterface|null
     *
     * @throws Exception
     * @throws OrmException
     * @see OrmCondition
     * @see OrmObjectlistOrderby
     */
    public function getSingleObject($strTargetClass, $strPrevid = "")
    {
        $arrParams = [];
        $strQuery = $this->getQuery($strTargetClass, $strPrevid, $arrParams);

        $arrRow = Carrier::getInstance()->getObjDB()->getPRow($strQuery, $arrParams);

        if (isset($arrRow["system_id"])) {
            OrmRowcache::addSingleInitRow($arrRow);
            return Objectfactory::getInstance()->getObject($arrRow["system_id"]);
        }

        return null;
    }

    /**
     * Builds the base select statement
     *
     * @param string|array $targetClass
     * @param string $strPrevid
     * @param array $arrParams
     * @param string $select
     * @param bool $orderBy
     * @return string
     * @throws Exception
     * @throws OrmException
     */
    private function getQuery($targetClass, $strPrevid, array &$arrParams, $select = "*", $orderBy = true)
    {
        $strQuery = "SELECT ".$select."
                       ".$this->getQueryBase($targetClass)."
                       ".($strPrevid != "" && $strPrevid !== null ? " AND system_prev_id = ? " : "");

        if ($strPrevid != "") {
            $arrParams[] = $strPrevid;
        }

        $this->addLogicalDeleteRestriction();
        $this->processWhereRestrictions($strQuery, $arrParams, $targetClass);

        if ($orderBy) {
            $strQuery .= $this->getOrderBy(new Reflection($targetClass));
        }

        return $strQuery;
    }

    /**
     * If we make an select on multiple classes we need to get the intersection of all available tables. At the minimum
     * we have the system table
     *
     * @param array $targetClasses
     * @return string
     */
    private function getIntersectSelect(array $targetClasses)
    {
        // find all tables which are available on both types
        $tableList = [];
        foreach ($targetClasses as $targetClass) {
            $tableList[] = $this->getTables($targetClass);
        }

        $tableList = array_intersect(...$tableList);

        $parts = [];
        $parts[] = "agp_system.*";
        foreach ($tableList as $table) {
            $parts[] = "{$table}.*";
        }

        return implode(", ", $parts);
    }

    /**
     * Returns all tables for a given target class
     *
     * @param string $targetClass
     * @return array
     * @throws Exception
     */
    private function getTables($targetClass)
    {
        $objAnnotations = new Reflection($targetClass);
        $arrTargetTables = $objAnnotations->getAnnotationValuesFromClass(OrmBase::STR_ANNOTATION_TARGETTABLE);

        $arrTables = array();
        foreach ($arrTargetTables as $strOneTable) {
            $arrOneTable = explode(".", $strOneTable);
            $arrTables[] = Carrier::getInstance()->getObjDB()->encloseTableName($arrOneTable[0]);
        }

        return $arrTables;
    }

    /**
     * Generates the order by statement
     *
     * @param Reflection $objReflection
     *
     * @return string
     */
    private function getOrderBy(Reflection $objReflection)
    {
        //try to load the sort criteria
        $arrPropertiesOrder = $objReflection->getPropertiesWithAnnotation(OrmBase::STR_ANNOTATION_LISTORDER);

        $bitHasCreateDate = false;
        $arrOrderByCriteria = array();
        foreach ($this->arrOrderBy as $objOneOrder) {
            $strOrderBy = $objOneOrder->getStrOrderBy();
            $arrOrderByCriteria[] = $strOrderBy;
            if (strpos($strOrderBy, "system_create_date") !== false) {
                $bitHasCreateDate = true;
            }
        }


        if (count($arrPropertiesOrder) > 0) {
            $arrPropertiesORM = $objReflection->getPropertiesWithAnnotation(OrmBase::STR_ANNOTATION_TABLECOLUMN);

            foreach ($arrPropertiesOrder as $strProperty => $strAnnotation) {
                if (isset($arrPropertiesORM[$strProperty])) {

                    $arrColumn = explode(".", $arrPropertiesORM[$strProperty]);
                    if (count($arrColumn) == 2) {
                        $strColumn = $arrColumn[1];
                    }
                    else {
                        $strColumn = $arrColumn[0];
                    }

                    //get order
                    $strOrder = (StringUtil::toUpperCase($strAnnotation) == "DESC" ? "DESC" : "ASC");

                    //get column
                    if ($strColumn != "") {
                        $arrOrderByCriteria[] = " ".$strColumn." ".$strOrder." ";

                        if (strpos($strColumn, "system_create_date") !== false) {
                            $bitHasCreateDate = true;
                        }
                    }
                }
            }
        }

        $arrOrderByCriteria[] = " CASE WHEN system_sort < 0 THEN 9999999 ELSE system_sort END ASC "; //TODO: add a better way of setting the max value

        if (!$bitHasCreateDate) {
            $arrOrderByCriteria[] = " system_create_date DESC ";
        }

        $arrOrderByCriteria = array_unique($arrOrderByCriteria);

        $strOrderBy = "";
        if (count($arrOrderByCriteria) > 0) {
            $strOrderBy = "ORDER BY ".implode(" , ", $arrOrderByCriteria)." ";
        }

        return $strOrderBy;
    }

    /**
     * Returns the default order by for the union query
     *
     * @param array $targetClass
     * @return string
     */
    private function getUnionOrderBy(array $targetClass)
    {
        // we order the result at first based on the class and then use the default sorting
        $parts[] = "system_class ASC";
        $parts[] = "system_sort ASC";
        $parts[] = "system_create_date DESC";

        return " ORDER BY ".implode(", ", $parts)." ";
    }

    protected function addLogicalDeleteRestriction()
    {
        $this->addWhereRestriction(new OrmCondition($this->getDeletedWhereRestriction("", ""), array()));
    }


    /**
     * Internal helper, adds the where restrictions
     *
     * @param string &$strQuery
     * @param array &$arrParams
     *
     * @return void
     */
    private function processWhereRestrictions(&$strQuery, &$arrParams, $strTargetClass)
    {
        foreach ($this->arrWhereRestrictions as $objOneRestriction) {
            $objOneRestriction->setStrTargetClass($strTargetClass);

            $strWhere = $objOneRestriction->getStrWhere();

            if ($objOneRestriction instanceof OrmConditionInterface && $strWhere != "") {
                $strWhere = OrmCondition::STR_CONDITION_AND." (".$strWhere.")";
            }

            $strQuery .= " ".$strWhere." ";
            foreach ($objOneRestriction->getArrParams() as $strOneParam) {
                $arrParams[] = $strOneParam;
            }
        }
    }


    /**
     * Add a where restriction to the current queries
     *
     * @param OrmConditionInterface $objCondition
     *
     * @return void
     */
    public function addWhereRestriction(OrmConditionInterface $objCondition)
    {
        $this->arrWhereRestrictions[] = $objCondition;
    }

    /**
     * Add a order by restriction to the current queries
     *
     * @param OrmObjectlistOrderby $objOrder
     *
     * @return void
     */
    public function addOrderBy(OrmObjectlistOrderby $objOrder)
    {
        $this->arrOrderBy[] = $objOrder;
    }


}
