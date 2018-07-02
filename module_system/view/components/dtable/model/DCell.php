<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\DTable\Model\DCell;

/**
 * DCell class.
 * Specifies a cell for using in DRow class objects.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 */
class DCell
{
    private $value;

    private $colspan = 0;

    /**
     * DCell constructor.
     * @param string $value
     */
    function __construct($value) {
        $this->setValue($value);
    }

    /**
     * @param string $value
     */
    function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getColspan():int
    {
        return $this->colspan;
    }

    /**
     * @param int $colspan
     */
    public function setColspan(int $colspan)
    {
        $this->colspan = $colspan;
    }

}