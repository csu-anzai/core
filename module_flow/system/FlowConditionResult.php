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

    /**
     * @param bool|null $bitValid
     */
    public function __construct($bitValid = null)
    {
        $this->bitValid = $bitValid;
        $this->arrErrors = [];
        $this->menuItems = [];
    }

    /**
     * In case no explicit valid status is set the result depends on the error count. If we have an explicit valid
     * status the result is independent of the error count
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->bitValid === null ? count($this->arrErrors) === 0 : $this->bitValid;
    }

    /**
     * @param string $strError
     */
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

    /**
     * Merges the status of another result object into this object. If both conditions have an explicit valid status
     * both conditions must also be true otherwise the valid status will be false. If this object has no explicit valid
     * status we take the status from the provided object
     *
     * @param FlowConditionResult $objResult
     */
    public function merge(FlowConditionResult $objResult)
    {
        $this->bitValid = $this->bitValid === null ? $objResult->isValid() : ($this->bitValid && $objResult->isValid());
        $this->arrErrors = array_merge($this->arrErrors, $objResult->getErrors());
        $this->menuItems = array_merge($this->menuItems, $objResult->getMenuItems());
    }
}
