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
    protected $isValid;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var MenuItem[]
     */
    protected $menuItems;

    /**
     * @param bool|null $bitValid
     */
    public function __construct($bitValid = null, array $errors = [], array $menuItems = [])
    {
        $this->isValid = $bitValid;
        $this->errors = $errors;
        $this->menuItems = $menuItems;
    }

    /**
     * In case no explicit valid status is set the result depends on the error count. If we have an explicit valid
     * status the result is independent of the error count
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid === null ? count($this->errors) === 0 : $this->isValid;
    }

    /**
     * @param string $strError
     */
    public function addError(string $strError)
    {
        $this->errors[] = $strError;
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
        return $this->errors;
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
        $this->isValid = $this->isValid === null ? $objResult->isValid() : ($this->isValid && $objResult->isValid());
        $this->errors = array_merge($this->errors, $objResult->getErrors());
        $this->menuItems = array_merge($this->menuItems, $objResult->getMenuItems());
    }
}
