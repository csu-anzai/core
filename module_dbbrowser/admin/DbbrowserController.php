<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/


declare(strict_types = 1);

namespace Kajona\Dbbrowser\Admin;

use Kajona\Search\System\SearchCommons;
use Kajona\Search\System\SearchResult;
use Kajona\Search\System\SearchSearch;
use Kajona\System\Admin\AdminEvensimpler;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\AdminInterface;
use Kajona\System\Admin\AdminSimple;
use Kajona\System\Admin\Formentries\FormentryHidden;
use Kajona\System\System\AdminListableInterface;
use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\ArraySectionIterator;
use Kajona\System\System\Carrier;
use Kajona\System\System\Db\Schema\TableKey;
use Kajona\System\System\Exception;
use Kajona\System\System\HttpResponsetypes;
use Kajona\System\System\Link;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\Resourceloader;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\StringUtil;
use Kajona\System\System\SystemModule;
use Kajona\System\View\Components\DTable\DTableComponent;
use Kajona\System\View\Components\DTable\Model\DCell;
use Kajona\System\View\Components\DTable\Model\DRow;
use Kajona\System\View\Components\DTable\Model\DTable;
use Kajona\System\View\Components\Grid\Grid;


/**
 *
 * @author stefan.idler@artemeonde
 *
 * @module dbbrowser
 * @moduleId _dbbrowser_module_id_
 */
class DbbrowserController extends AdminEvensimpler
{

    /**
     * @return array
     */
    public function getOutputModuleNavi()
    {
        $arrReturn = array();
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "list", "", $this->getLang("action_list")));
        return $arrReturn;
    }


    /**
     * Creates a form to edit systemsettings or updates them
     *
     * @return string "" in case of success
     * @autoTestable
     * @permissions view
     * @throws Exception
     */
    protected function actionList()
    {
        $return = $this->objToolkit->formHeadline($this->getLang("schema_tables"));
        $return .= $this->objToolkit->listHeader();
        foreach (Carrier::getInstance()->getObjDB()->getTables() as $tableName) {
            $details = Link::getLinkAdminXml($this->getArrModule("module"), "apiSystemSchema", ["table" => $tableName]);
            $link = Link::getLinkAdminManual("href=\"#\" onclick=\"require('ajax').loadUrlToElement('.schemaDetails', '{$details}'); return false;\"", $tableName);
            $return .= $this->objToolkit->genericAdminList("", $link, AdminskinHelper::getAdminImage("icon_table"), "");
        }
        $return .= $this->objToolkit->listFooter();

        $grid = new Grid([3, 9]);
        $grid->setBitLimitHeight(true);
        $grid->addRow([$return, "<div class='schemaDetails'></div>"]);
        return $grid->renderComponent();
    }

    /**
     * Creates a form to edit systemsettings or updates them
     *
     * @return string "" in case of success
     * @permissions view
     * @throws Exception
     * @responseType html
     */
    protected function actionApiSystemSchema()
    {
        $tableName = $this->getParam("table");
        $details = Carrier::getInstance()->getObjDB()->getTableInformation($tableName);


        $arrIndexPlain = array_map(function(TableKey $key) {
            return $key->getName();
        }, $details->getPrimaryKeys());

        $tableColumns = new DTable();
        $tableColumns->addHeader(new DRow([
            "",
            "",
            $this->getLang("schema_header_name"),
            $this->getLang("schema_header_type_int"),
            $this->getLang("schema_header_type_db"),
            $this->getLang("schema_header_type_null"),
            ""
        ]));
        foreach ($details->getColumns() as $column) {
            $tableColumns->addRow(
                new DRow([
                    (new DCell(AdminskinHelper::getAdminImage("icon_column")))->setClassAddon("width-20-px"),
                    (new DCell(in_array($column->getName(), $arrIndexPlain) ? AdminskinHelper::getAdminImage("icon_key") : ""))->setClassAddon("width-20-px"),
                    $column->getName(),
                    $column->getInternalType(),
                    $column->getDatabaseType(),
                    $column->isNullable() === true ? "null" : "not null",
                    (new DCell($this->objToolkit->listButton(AdminskinHelper::getAdminImage("icon_index"))))->setClassAddon("align-right")
                ])
            );
        }

        $tableKeys = new DTable();
        $tableKeys->addHeader(new DRow([
            "",
            $this->getLang("schema_header_name"),
        ]));
        foreach ($details->getPrimaryKeys() as $key) {
            $tableKeys->addRow(
                new DRow([
                    (new DCell(AdminskinHelper::getAdminImage("icon_key")))->setClassAddon("width-20-px"),
                    $key->getName()
                ])
            );
        }

        $tableIndex = new DTable();
        $tableIndex->addHeader(new DRow([
            "",
            $this->getLang("schema_header_name"),
            $this->getLang("schema_header_type_def"),
            ""
        ]));
        foreach ($details->getIndexes() as $index) {
            $tableIndex->addRow(
                new DRow([
                    (new DCell(AdminskinHelper::getAdminImage("icon_index")))->setClassAddon("width-20-px"),
                    $index->getName(),
                    $index->getDescription(),
                    (new DCell($this->objToolkit->listButton(AdminskinHelper::getAdminImage("icon_delete")).
                        $this->objToolkit->listButton(AdminskinHelper::getAdminImage("icon_sync"))))->setClassAddon("align-right")
                ])
            );
        }

        $return = "";
        $return .= $this->objToolkit->formHeadline($tableName);
        $return .= $this->objToolkit->getFieldset($this->getLang("schema_columns"), (new DTableComponent($tableColumns))->renderComponent());
        $return .= $this->objToolkit->getFieldset($this->getLang("schema_keys"), (new DTableComponent($tableKeys))->renderComponent());
        $return .= $this->objToolkit->getFieldset($this->getLang("schema_indexes"), (new DTableComponent($tableIndex))->renderComponent());

        return $return;
    }


}
