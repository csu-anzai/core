<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Flow\System;

use Kajona\System\View\Components\Menu\MenuItem;

/**
 * A result object of a condition
 *
 * @author christoph.kappestein@artemeon.de
 * @module flow
 */
class FlowConditionResult
{
    /**
     * @var bool
     */
    protected $bitValid;

    /**
     * @var array
     */
    protected $arrErrors;

    /**
     * @var MenuItem[]
     */
    protected $menuItems;

    public function __construct($bitValid = null)
    {
        $this->bitValid = $bitValid;
        $this->arrErrors = [];
        $this->menuItems = [];
    }

    public function isValid()
    {
        return $this->bitValid === null ? count($this->arrErrors) === 0 : $this->bitValid;
    }

    public function addError(string $strError)
    {
        $this->arrErrors[] = $strError;
    }

    /**
     * @param MenuItem $menuItem
     */
    public function addMenuItem(MenuItem $menuItem)
    {
        $this->menuItems[] = $menuItem;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->arrErrors;
    }

    /**
     * @return MenuItem[]
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    public function merge(FlowConditionResult $objResult)
    {
        $this->bitValid = $this->bitValid === null ? $objResult->isValid() : ($this->bitValid && $objResult->isValid());
        $this->arrErrors = array_merge($this->arrErrors, $objResult->getErrors());
        $this->menuItems = array_merge($this->menuItems, $objResult->getMenuItems());
    }
}
