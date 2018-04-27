<?php

namespace Kajona\System\Tests\Benchmarks;

use AGP\Prozessverwaltung\System\ProzessverwaltungProzess;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\OrmObjectinit;

/**
 * ORMBench
 *
 * @Revs(10)
 * @Iterations(10)
 */
class ORMBench
{
    public function benchInitObjectFromDb()
    {
        $objObject = new ProzessverwaltungProzess();
        $objORM = new OrmObjectinit($objObject);
        $objORM->initObjectFromDb();
    }

    public function benchUpdateObjectToDb()
    {
        $objObject = new ProzessverwaltungProzess();
        $objObject->setStrTitel("foobar");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objObject))->update($objObject);
    }
}
