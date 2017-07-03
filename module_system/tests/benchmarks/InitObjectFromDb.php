<?php

namespace Kajona\System\Tests\Benchmarks;

use AGP\Prozessverwaltung\System\ProzessverwaltungProzess;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\OrmObjectinit;
use Kajona\System\System\OrmObjectinitNew;

/**
 * InitObjectFromDb
 *
 * @Revs(1000)
 * @Iterations(10)
 */
class InitObjectFromDb
{
    public function benchInit()
    {
        $objObject = new ProzessverwaltungProzess();
        $objORM = new OrmObjectinit($objObject);
        $objORM->initObjectFromDb();
    }

    public function benchInitNew()
    {
        $objObject = new ProzessverwaltungProzess();
        $objORM = new OrmObjectinitNew($objObject);
        $objORM->initObjectFromDb();
    }
}
