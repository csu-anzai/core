<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Search\System;

use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\SearchResultobjectInterface;
use Kajona\System\System\StringUtil;
use Kajona\System\System\SystemModule;

/**
 * This class contains a few methods used by the search as little helpers
 *
 * @package module_search
 * @author sidler@mulchprod.de
 *
 * @module search
 * @moduleId _search_module_id_
 */
class SearchCommons extends Model implements ModelInterface
{

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return "";
    }

    /**
     * Calls the single search-functions, sorts the results and creates the output.
     * Method for backend-searches.
     *
     * @param SearchSearch $objSearch
     * @param int $intStart
     * @param int $intEnd
     *
     * @return SearchResult[]
     */
    public function doAdminSearch(SearchSearch $objSearch, $intStart = null, $intEnd = null)
    {

        $arrHits = $this->doIndexedSearch($objSearch, $intStart, $intEnd);

        //if the object is an instance of SearchResultobjectInterface, the target-link may be updated
        foreach ($arrHits as $objOneResult) {
            if ($objOneResult->getObjObject() instanceof SearchResultobjectInterface) {
                $objOneResult->setStrPagelink($objOneResult->getObjObject()->getSearchAdminLinkForObject());
            }
        }

        return $arrHits;
    }


    /**
     * @param SearchSearch $objSearch
     * @param null $intStart
     * @param null $intEnd
     *
     * @return SearchResult[]
     */
    public function doIndexedSearch($objSearch, $intStart = null, $intEnd = null)
    {
        $arrHits = array();

        $objParser = new SearchQueryParser();
        $objSearchQuery = $objParser->parseText($objSearch->getStrQuery());
        if ($objSearchQuery == null) {
            return array();
        }

        $objSearchQuery->setMetadataFilter($this->getMetadataFilterFromSearch($objSearch));

        $strQuery = "";
        $arrParameters = array();
        $objSearchQuery->getListQuery($strQuery, $arrParameters);
        $arrSearchResult = $this->objDB->getPArray($strQuery, $arrParameters, $intStart, $intEnd);

        // check view permissions on both, record and matching module
        foreach ($arrSearchResult as $arrOneRow) {
            $objInstance = Objectfactory::getInstance()->getObject($arrOneRow["search_ix_system_id"]);

            $objModule = $objInstance != null ? SystemModule::getModuleByName($objInstance->getArrModule("modul")) : null;
            if ($objInstance != null && $objModule != null && $objInstance->rightView() && $objModule->rightView()) {
                $objResult = new SearchResult();
                $objResult->setObjSearch($objSearch);
                $objResult->setObjObject($objInstance);
                $objResult->setIntScore($arrOneRow["score"]);
                $arrHits[] = $objResult;
            }
        }

        //log the query
        SearchLog::generateLogEntry($objSearch->getStrQuery());

        return $arrHits;
    }


    /**
     * Counts the number of hits
     *
     * @param SearchSearch $objSearch
     *
     * @return int
     */
    public function getIndexedSearchCount($objSearch)
    {
        $objParser = new SearchQueryParser();
        $objSearchQuery = $objParser->parseText($objSearch->getStrQuery());

        if ($objSearchQuery == null) {
            return 0;
        }

        $objSearchQuery->setMetadataFilter($this->getMetadataFilterFromSearch($objSearch));

        $strQuery = "";
        $arrParameters = array();
        $objSearchQuery->getCountQuery($strQuery, $arrParameters);
        $arrSearchResult = $this->objDB->getPRow($strQuery, $arrParameters);
        return $arrSearchResult["cnt"];
    }

    /**
     * @param SearchSearch $objSearch
     *
     * @return SearchMetadataFilter
     */
    private function getMetadataFilterFromSearch($objSearch)
    {
        $objMetadataFilter = new SearchMetadataFilter();
        $objMetadataFilter->setFilterModules($objSearch->getFilterModules());
        $objMetadataFilter->setFilterUser($objSearch->getFilterUser());
        $objMetadataFilter->setFilterChangeStartDate($objSearch->getObjChangeStartdate());
        $objMetadataFilter->setFilterChangeEndDate($objSearch->getObjChangeEnddate());
        $objMetadataFilter->setBitPortalSearch($objSearch->getBitPortalObjectFilter());
        $objMetadataFilter->setStrPortalLang($objSearch->getStrPortalLangFilter());
        return $objMetadataFilter;
    }
}
