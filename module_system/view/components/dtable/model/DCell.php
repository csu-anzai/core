<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\DTable\Model;

/**
 * DCell class.
 * Specifies a cell for using in DRow class objects.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 */
class DCell
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $colspan = 0;

    /**
     * @var string
     */
    private $classAddon = '';

    /**
     * DCell constructor.
     * @param string $value
     */
    public function __construct($value) {
        $this->setValue($value);
    }

    /**
     * @param string $value
     *
     * @return DCell
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
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
     *
     * @return DCell
     */
    public function setColspan(int $colspan)
    {
        $this->colspan = $colspan;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassAddon(): string
    {
        return $this->classAddon;
    }

    /**
     * @param string $classAddon
     *
     * @return DCell
     */
    public function setClassAddon(string $classAddon)
    {
        $this->classAddon = $classAddon;

        return $this;
    }

}