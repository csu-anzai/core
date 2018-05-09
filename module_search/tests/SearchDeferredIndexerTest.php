<?php

namespace Kajona\Search\Tests;

use Kajona\Search\Event\SearchRequestEndprocessinglistener;
use Kajona\Search\System\SearchEnumIndexaction;
use Kajona\Search\System\SearchIndexqueue;
use Kajona\System\System\Database;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\SystemChangelog;
use Kajona\System\System\SystemEventidentifier;
use Kajona\System\System\SystemSetting;
use Kajona\System\Tests\Testbase;

class SearchDeferredIndexerTest extends Testbase
{


    public function testObjectIndexer()
    {
        $objConfig = SystemSetting::getConfigByName("_search_deferred_indexer_");
        $objConfig->setStrValue("true");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objConfig))->update($objConfig);

        $objObject = new MessagingMessage();
        $objObject->setStrTitle("unittest demo message");
        $objObject->setStrBody("unittest demo message body");
        $objObject->setStrMessageProvider("Kajona\\System\\System\\Messageproviders\\MessageproviderPersonalmessage");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objObject))->update($objObject);
        $strObjectId = $objObject->getSystemid();

        //trigger the endprocessinglistener
        $objHandler = new SearchRequestEndprocessinglistener();
        $objHandler->handleEvent(SystemEventidentifier::EVENT_SYSTEM_REQUEST_AFTERCONTENTSEND, array());


        //query queue table
        $objQueue = new SearchIndexqueue();
        $arrRows = $objQueue->getRowsBySystemid(SearchEnumIndexaction::INDEX(), $strObjectId);
        $this->assertTrue(count($arrRows) == 1);
        $this->assertTrue($arrRows[0]["search_queue_systemid"] == $objObject->getSystemid());


        Objectfactory::getInstance()->getObject($strObjectId)->deleteObjectFromDatabase();
        $objHandler->handleEvent(SystemEventidentifier::EVENT_SYSTEM_REQUEST_AFTERCONTENTSEND, array());


        $arrRows = $objQueue->getRowsBySystemid(SearchEnumIndexaction::DELETE(), $strObjectId);
        $this->assertTrue(count($arrRows) == 1);
        $this->assertTrue($arrRows[0]["search_queue_systemid"] == $objObject->getSystemid());


        $objConfig = SystemSetting::getConfigByName("_search_deferred_indexer_");
        $objQueue->deleteBySystemid($strObjectId);
        $objConfig->setStrValue("false");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objConfig))->update($objConfig);

    }

    public function testObjectIndexerPerformance()
    {
        $arrObjectIds = array();


        //echo "Indexing without deferred indexer...\n";
        SystemChangelog::$bitChangelogEnabled = false;
        $intTimeStart = microtime(true);
        $intQueriesStart = Database::getInstance()->getNumber();

        for ($intI = 0; $intI < 15; $intI++) {
            $objObject = new MessagingMessage();
            $objObject->setStrTitle("unittest demo message");
            $objObject->setStrBody("unittest demo message body");
            $objObject->setStrMessageProvider("Kajona\\System\\System\\Messageproviders\\MessageproviderPersonalmessage");
            ServiceLifeCycleFactory::getLifeCycle(get_class($objObject))->update($objObject);
            $arrObjectIds[] = $objObject->getSystemid();
        }

        //echo "Queries pre indexing: ", Database::getInstance()->getNumber() - $intQueriesStart . " \n";

        $objHandler = new SearchRequestEndprocessinglistener();
        $objHandler->handleEvent(SystemEventidentifier::EVENT_SYSTEM_REQUEST_AFTERCONTENTSEND, array());

        $intTimeEnd = microtime(true);
        $time = $intTimeEnd - $intTimeStart;
        //echo "Object updates: ", sprintf('%f', $time), " sec.\n";
        //echo "Queries total: ", Database::getInstance()->getNumber() - $intQueriesStart . " \n";


        //echo "\nIndexing with deferred indexer...\n";
        $objConfig = SystemSetting::getConfigByName("_search_deferred_indexer_");
        $objConfig->setStrValue("true");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objConfig))->update($objConfig);

        $intTimeStart = microtime(true);
        $intQueriesStart = Database::getInstance()->getNumber();

        for ($intI = 0; $intI < 15; $intI++) {
            $objObject = new MessagingMessage();
            $objObject->setStrTitle("unittest demo message");
            $objObject->setStrBody("unittest demo message body");
            $objObject->setStrMessageProvider("Kajona\\System\\System\\Messageproviders\\MessageproviderPersonalmessage");
            ServiceLifeCycleFactory::getLifeCycle(get_class($objObject))->update($objObject);
            $arrObjectIds[] = $objObject->getSystemid();
        }

        //echo "Queries pre indexing: ", Database::getInstance()->getNumber() - $intQueriesStart . " \n";

        //echo "Triggering queue update event...\n";
        $objHandler = new SearchRequestEndprocessinglistener();
        $objHandler->handleEvent(SystemEventidentifier::EVENT_SYSTEM_REQUEST_AFTERCONTENTSEND, array());

        $intTimeEnd = microtime(true);
        $time = $intTimeEnd - $intTimeStart;
        //echo "Object updates: ", sprintf('%f', $time), " sec.\n";
        //echo "Queries total: ", Database::getInstance()->getNumber() - $intQueriesStart . " \n";


        $objConfig = SystemSetting::getConfigByName("_search_deferred_indexer_");
        $objConfig->setStrValue("false");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objConfig))->update($objConfig);

        foreach ($arrObjectIds as $strObjectId) {
            Objectfactory::getInstance()->getObject($strObjectId)->deleteObjectFromDatabase();
        }

        $this->assertTrue(true);//dummy assertion to make test not risky. Until here no exception should have occurred

    }
}


