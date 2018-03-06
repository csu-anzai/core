<?php

namespace Kajona\System\Tests\Benchmarks;

/**
 * MethodExistsInstanceOfBench
 *
 * @Revs(10000)
 * @Iterations(10)
 */
class MethodExistsInstanceOfBench
{
    public function benchMethodExists()
    {
        $objFoo = new Foo();

        if (method_exists($objFoo, "setArrInitRow")) {
        }
    }

    public function benchInstanceOf()
    {
        $objFoo = new Foo();

        if ($objFoo instanceof Foo) {
        }
    }
}

class Foo
{
    public function setArrInitRow($arrInitRow)
    {
    }
}
