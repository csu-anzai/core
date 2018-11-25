<?php

namespace Kajona\System\Tests;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;

class AdminFormsTest extends Testbase
{


    public function testFormManager()
    {

        $objFormManager = new AdminFormgenerator("test", new AdminFormB());

        $objFormManager->generateFieldsFromObject();


        $this->assertNotNull($objFormManager->getField("fielda1"));
        $this->assertNotNull($objFormManager->getField("fielda2"));
        $this->assertNotNull($objFormManager->getField("fieldb1"));
        $this->assertNotNull($objFormManager->getField("fieldb2"));

        $arrFields = $objFormManager->getArrFields();
        $arrKey = array_keys($arrFields);

        $this->assertEquals($arrKey[0], "fielda1");
        $this->assertEquals($arrKey[1], "fielda2");
        $this->assertEquals($arrKey[2], "fieldb1");
        $this->assertEquals($arrKey[3], "fieldb2");

        $objFormManager->setFieldToPosition("fielda2", 1);
        $objFormManager->setFieldToPosition("fieldb2", 4);

        $arrFields = $objFormManager->getArrFields();
        $arrKey = array_keys($arrFields);

        $this->assertEquals($arrKey[0], "fielda2");
        $this->assertEquals($arrKey[1], "fielda1");
        $this->assertEquals($arrKey[2], "fieldb1");
        $this->assertEquals($arrKey[3], "fieldb2");
    }

    public function testFloatCompleteness()
    {

        $objSourceobject = new AdminFormB();
        $objFormManager = new AdminFormgenerator("test", $objSourceobject);

        $objFormManager->generateFieldsFromObject();

        $fa1 = $objFormManager->getField("fielda1");
        $fa2 = $objFormManager->getField("fielda2");
        $fb1 = $objFormManager->getField("fieldb1");
        $fb2 = $objFormManager->getField("fieldb2");
        $fb3 = $objFormManager->getField("fieldb3");

        $this->assertFalse($fa1->getBitMandatory());
        $this->assertTrue($fa2->getBitMandatory());
        $this->assertTrue($fb1->getBitMandatory());
        $this->assertFalse($fb2->getBitMandatory());
        $this->assertTrue($fb3->getBitMandatory());

        $countRequiredFields = count($objFormManager->getRequiredFields()); // 3
        $countErrorsFields = count($objFormManager->getValidationErrorObjects()); // 3
        $completeness = ($countRequiredFields - $countErrorsFields)*100/$countRequiredFields; // 0%

        $this->assertEquals($countRequiredFields, 3);
        $this->assertEquals($countErrorsFields, 3);
        $this->assertEquals($objFormManager->getFloatFormCompleteness(), $completeness);
        $objFormManager->removeAllValidationError();

        $fa1->setStrValue('fa1 value');
        $countErrorsFields = count($objFormManager->getValidationErrorObjects()); // 3
        $completeness = ($countRequiredFields - $countErrorsFields)*100/$countRequiredFields; // 0%

        $this->assertEquals($countErrorsFields, 3);
        $this->assertEquals($objFormManager->getFloatFormCompleteness(), $completeness);
        $objFormManager->removeAllValidationError();

        $objSourceobject->setStrFieldA2('fa2 value');
        $countErrorsFields = count($objFormManager->getValidationErrorObjects()); // 2
        $completeness = round(($countRequiredFields - $countErrorsFields)*100/$countRequiredFields, 2); // 33.33%

        $this->assertEquals($countErrorsFields, 2);
        $this->assertEquals($objFormManager->getFloatFormCompleteness(), $completeness);
        $objFormManager->removeAllValidationError();

        $objSourceobject->setStrFieldB1('fb1 value');
        $objSourceobject->setStrFieldB2('fb2 value');
        $countErrorsFields = count($objFormManager->getValidationErrorObjects()); // 1
        $completeness = round(($countRequiredFields - $countErrorsFields)*100/$countRequiredFields, 2); // 66.66%

        $this->assertEquals($countErrorsFields, 1);
        $this->assertEquals($objFormManager->getFloatFormCompleteness(), $completeness);
        $objFormManager->removeAllValidationError();

        $objSourceobject->setStrFieldB3('fb3 value');
        $countErrorsFields = count($objFormManager->getValidationErrorObjects()); // 0
        $completeness = ($countRequiredFields - $countErrorsFields)*100/$countRequiredFields; // 100%

        $this->assertEquals($countErrorsFields, 0);
        $this->assertEquals($objFormManager->getFloatFormCompleteness(), $completeness);
        $objFormManager->removeAllValidationError();
    }

}

//set up test-structures

class AdminFormA extends Model
{

    /**
     * @var
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     */
    private $strFieldA1;

    /**
     * @var
     * @fieldMandatory
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     */
    private $strFieldA2;

    /**
     * @param  $strFieldA1
     */
    public function setStrFieldA1($strFieldA1)
    {
        $this->strFieldA1 = $strFieldA1;
    }

    /**
     * @return
     */
    public function getStrFieldA1()
    {
        return $this->strFieldA1;
    }

    /**
     * @param  $strFieldA2
     */
    public function setStrFieldA2($strFieldA2)
    {
        $this->strFieldA2 = $strFieldA2;
    }

    /**
     * @return
     */
    public function getStrFieldA2()
    {
        return $this->strFieldA2;
    }


}

class AdminFormB extends AdminFormA
{

    /**
     * @var
     * @fieldMandatory
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     */
    private $strFieldB1;

    /**
     * @var
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     */
    private $strFieldB2;

    /**
     * @var
     * @fieldMandatory
     * @fieldType Kajona\System\Admin\Formentries\FormentryText
     */
    private $strFieldB3;

    /**
     * @param  $strFieldB1
     */
    public function setStrFieldB1($strFieldB1)
    {
        $this->strFieldB1 = $strFieldB1;
    }

    /**
     * @return
     */
    public function getStrFieldB1()
    {
        return $this->strFieldB1;
    }

    /**
     * @param  $strFieldB2
     */
    public function setStrFieldB2($strFieldB2)
    {
        $this->strFieldB2 = $strFieldB2;
    }

    /**
     * @return
     */
    public function getStrFieldB2()
    {
        return $this->strFieldB2;
    }

    /**
     * @param  $strFieldB2
     */
    public function setStrFieldB3($strFieldB3)
    {
        $this->strFieldB3 = $strFieldB3;
    }

    /**
     * @return
     */
    public function getStrFieldB3()
    {
        return $this->strFieldB3;
    }

}

