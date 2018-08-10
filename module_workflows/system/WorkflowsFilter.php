<?php
/*"******************************************************************************************************
*   (c) 2010-2018 ARTEMEON                                                                              *
********************************************************************************************************/

namespace Kajona\Workflows\System;

use Kajona\System\System\FilterBase;

/**
 *
 * @module workflows
 * @moduleId _workflows_module_id_
 */
class WorkflowsFilter extends FilterBase
{

    /**
     * @var string
     * @tableColumn workflows.workflows_class
     * @filterCompareOperator EQ
     */
    private $strClass = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_systemid
     */
    private $strAffectedSystemid = null;

    /**
     * @var array
     * @tableColumn workflows.workflows_state
     * @filterCompareOperator IN
     */
    private $arrState = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_responsible
     */
    private $strResponsible = null;

    /**
     * @var int
     * @tableColumn workflows.workflows_int1
     */
    private $intInt1 = null;

    /**
     * @var int
     * @tableColumn workflows.workflows_int2
     */
    private $intInt2 = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_char1
     */
    private $strChar1 = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_char2
     */
    private $strChar2 = null;

    /**
     * @var int
     * @tableColumn workflows.workflows_date1
     */
    private $longDate1 = null;

    /**
     * @var int
     * @tableColumn workflows.workflows_date2
     */
    private $longDate2 = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_text
     * @filterCompareOperator LIKE
     */
    private $strText = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_text2
     */
    private $strText2 = null;

    /**
     * @var string
     * @tableColumn workflows.workflows_text3
     */
    private $strText3 = null;

    /**
     * @return string
     */
    public function getStrClass()
    {
        return $this->strClass;
    }

    /**
     * @param string $strClass
     */
    public function setStrClass(string $strClass)
    {
        $this->strClass = $strClass;
    }

    /**
     * @return string
     */
    public function getStrAffectedSystemid()
    {
        return $this->strAffectedSystemid;
    }

    /**
     * @param string $strAffectedSystemid
     */
    public function setStrAffectedSystemid(string $strAffectedSystemid)
    {
        $this->strAffectedSystemid = $strAffectedSystemid;
    }

    /**
     * @return array
     */
    public function getArrState()
    {
        return $this->arrState;
    }

    /**
     * @param array $arrState
     */
    public function setArrState(array $arrState)
    {
        $this->arrState = $arrState;
    }


    /**
     * @return string
     */
    public function getStrResponsible()
    {
        return $this->strResponsible;
    }

    /**
     * @param string $strResponsible
     */
    public function setStrResponsible(string $strResponsible)
    {
        $this->strResponsible = $strResponsible;
    }

    /**
     * @return int
     */
    public function getIntInt1()
    {
        return $this->intInt1;
    }

    /**
     * @param int $intInt1
     */
    public function setIntInt1(int $intInt1)
    {
        $this->intInt1 = $intInt1;
    }

    /**
     * @return int
     */
    public function getIntInt2()
    {
        return $this->intInt2;
    }

    /**
     * @param int $intInt2
     */
    public function setIntInt2(int $intInt2)
    {
        $this->intInt2 = $intInt2;
    }

    /**
     * @return string
     */
    public function getStrChar1()
    {
        return $this->strChar1;
    }

    /**
     * @param string $strChar1
     */
    public function setStrChar1(string $strChar1)
    {
        $this->strChar1 = $strChar1;
    }

    /**
     * @return string
     */
    public function getStrChar2()
    {
        return $this->strChar2;
    }

    /**
     * @param string $strChar2
     */
    public function setStrChar2(string $strChar2)
    {
        $this->strChar2 = $strChar2;
    }

    /**
     * @return int
     */
    public function getLongDate1()
    {
        return $this->longDate1;
    }

    /**
     * @param int $longDate1
     */
    public function setLongDate1(int $longDate1)
    {
        $this->longDate1 = $longDate1;
    }

    /**
     * @return int
     */
    public function getLongDate2()
    {
        return $this->longDate2;
    }

    /**
     * @param int $longDate2
     */
    public function setLongDate2(int $longDate2)
    {
        $this->longDate2 = $longDate2;
    }

    /**
     * @return string
     */
    public function getStrText()
    {
        return $this->strText;
    }

    /**
     * @param string $strText
     */
    public function setStrText(string $strText)
    {
        $this->strText = $strText;
    }

    /**
     * @return string
     */
    public function getStrText2()
    {
        return $this->strText2;
    }

    /**
     * @param string $strText2
     */
    public function setStrText2(string $strText2)
    {
        $this->strText2 = $strText2;
    }

    /**
     * @return string
     */
    public function getStrText3()
    {
        return $this->strText3;
    }

    /**
     * @param string $strText3
     */
    public function setStrText3(string $strText3)
    {
        $this->strText3 = $strText3;
    }


}
