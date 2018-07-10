<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\DTable;

use Kajona\System\View\Components\AbstractComponent;
use Kajona\System\View\Components\DTable\Model\DTable;


/**
 * DataTable component. You need to specify the DTable object.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 * @componentTemplate core/module_system/view/components/dtable/template.twig
 */
class DTableComponent extends AbstractComponent
{
    /**
     * @var DTable
     */

    private $dataTable;

    /**
     * @var string
     */
    private $tableCssAddon = "";

    /**
     * @var bool
     */
    private $withTbody = false;

    /**
     * DTableComponent constructor.
     * @param DTable $dataTable
     */
    public function __construct(DTable $dataTable)
    {
        parent::__construct();

        $this->dataTable = $dataTable;
    }

    /**
     * @return string
     */
    public function getTableCssAddon(): string
    {
        return $this->tableCssAddon;
    }

    /**
     * @param string $tableCssAddon
     */
    public function setTableCssAddon(string $tableCssAddon)
    {
        $this->tableCssAddon = $tableCssAddon;
    }

    /**
     * @return bool
     */
    public function isWithTbody(): bool
    {
        return $this->withTbody;
    }

    /**
     * @param bool $withTbody
     */
    public function setWithTbody(bool $withTbody)
    {
        $this->withTbody = $withTbody;
    }


    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "headers" => $this->dataTable->getHeaders(),
            "rows" => $this->dataTable->getRows(),
            "tableCssAddon" => $this->getTableCssAddon(),
            "withTbody" => $this->isWithTbody(),
        ];

        return $this->renderTemplate($data);
    }
}
