<?php

namespace Kajona\System\Tests\Filter;

use AGP\Prozessverwaltung\Admin\Formentries\FormentryObjectGroups;
use Kajona\System\Admin\AdminEvensimpler;
use Kajona\System\Admin\AdminInterface;
use Kajona\System\System\Classloader;
use Kajona\System\System\Date;
use Kajona\System\System\FilterBase;
use Kajona\System\System\Reflection;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\Root;
use Kajona\System\System\StringUtil;
use Kajona\System\Tests\Testbase;

/**
 * Class class_test_functions
 */
class FilteListTest extends Testbase
{

    /**
     *
     */
    public function testFilterList()
    {
        $arrObject2Filter = $this->getObject2Filter();

        foreach($arrObject2Filter as $arrModelFilter) {
            $strFilter = $arrModelFilter["filter"];
            $strModel = $arrModelFilter["model"];

            /** @var FilterBase $objFilter */
            $filter = new $strFilter();

            /** @var Root $model */
            $model = new $strModel();

            $arrList = $model::getObjectListFiltered($filter);
            $this->assertNotNull($arrList);
            $this->assertTrue(is_array($arrList));
            $intCount = $model::getObjectCountFiltered($filter);
            $this->assertGreaterThanOrEqual(0,$intCount);
        }
    }

    /**
     *
     */
    public function testFilterListFiltered()
    {
        $arrObject2Filter = $this->getObject2Filter();

        foreach($arrObject2Filter as $arrModelFilter) {
            $strFilter = $arrModelFilter["filter"];
            $strModel = $arrModelFilter["model"];

            /** @var FilterBase $objFilter */
            $filter = new $strFilter();
            $this->setFilterValues($filter);

            /** @var Root $model */
            $model = new $strModel();


            $arrList = $model::getObjectListFiltered($filter);
            $this->assertNotNull($arrList);
            $this->assertTrue(is_array($arrList));
            $intCount = $model::getObjectCountFiltered($filter);
            $this->assertGreaterThanOrEqual(0,$intCount);
        }
    }

    /**
     * @return AdminInterface[]
     */
    private function getObject2Filter()
    {
        $arrObject2Filter  = array();

        //load classes
        $arrFiles = Resourceloader::getInstance()->getFolderContent("/admin", array(".php"), false, null, function (&$strFile, $strPath) {
            $strFile = Classloader::getInstance()->getInstanceFromFilename($strPath, AdminEvensimpler::class);
        });

        foreach($arrFiles as $objInstance) {
            if($objInstance == null) {
                continue;
            }

            $objReflection = new Reflection($objInstance);
            $arrAnnotations = $objReflection->getAnnotationsFromClass();


            //now find list and filter pairs
            foreach($arrAnnotations as $strAnnotation => $arrValuesParams) {
                if (StringUtil::startsWith($strAnnotation, AdminEvensimpler::STR_OBJECT_LISTFILTER_ANNOTATION)) {
                    $strName = StringUtil::substring($strAnnotation, StringUtil::length(AdminEvensimpler::STR_OBJECT_LISTFILTER_ANNOTATION));

                    //find also list annotation
                    if(array_key_exists(AdminEvensimpler::STR_OBJECT_LIST_ANNOTATION.$strName, $arrAnnotations)) {
                        $arrValuesListAnnotation = $arrAnnotations[AdminEvensimpler::STR_OBJECT_LIST_ANNOTATION . $strName];
                        $arrObject2Filter[] = [
                            "model" => $arrValuesListAnnotation["values"][0],
                            "filter" => $arrValuesParams["values"][0]
                        ];
                    }

                    //find also edit annotation
                    if(array_key_exists(AdminEvensimpler::STR_OBJECT_EDIT_ANNOTATION.$strName, $arrAnnotations)) {
                        $arrValuesListAnnotation = $arrAnnotations[AdminEvensimpler::STR_OBJECT_EDIT_ANNOTATION . $strName];
                        $arrObject2Filter[] = [
                            "model" => $arrValuesListAnnotation["values"][0],
                            "filter" => $arrValuesParams["values"][0]
                        ];
                    }
                }
            }
        }

        return $arrObject2Filter;
    }

    /**
     * @param FilterBase $filter
     * @throws \Kajona\System\System\Exception
     */
    private function setFilterValues(FilterBase $filter) {

        $reflection = new Reflection($filter);

        /** @var string[] $arrProperties */
        $arrProperties = $reflection->getPropertiesWithAnnotation("@tableColumn");

        foreach($arrProperties as $property => $annotationvalue) {
            $filtervalue = null;

            $annotationvalue_var = $reflection->getAnnotationValueForProperty($property, "@var");
            $annotationvalue_filedtype = $reflection->getAnnotationValueForProperty($property, "@fieldType");

            if($annotationvalue_var !== null) {
                switch($annotationvalue_var) {
                    case "int":
                        $filtervalue = 1;
                        break;
                    case "int[]":
                        $filtervalue = [0,1];
                        break;
                    case "string":
                        $filtervalue = "abc";
                        if ($annotationvalue_filedtype == "AGP\\Prozessverwaltung\\Admin\\Formentries\\FormentryObjectGroups") {
                            $filtervalue = "{}";
                        }
                        break;
                    case "string[]":
                        $filtervalue = ["abc", "def"];
                        break;
                    case "boolean":
                        $filtervalue = true;
                        break;
                    case "bool":
                        $filtervalue = true;
                        break;
                    case "array":
                        $filtervalue = [0,1];
                        break;
                    case "Date":
                        $filtervalue = new Date();
                        break;
                }
            }

            $setter = $reflection->getSetter($property);
            $filter->$setter($filtervalue);

        }
    }

}

