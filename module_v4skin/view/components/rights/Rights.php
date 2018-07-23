<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\V4skin\View\Components\Rights;

use Kajona\System\System\Lang;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Root;
use Kajona\System\System\Session;
use Kajona\System\System\SystemModule;
use Kajona\System\System\SystemSetting;
use Kajona\System\View\Components\AbstractComponent;

/**
 * Component to render the right matrix
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 * @componentTemplate core/module_v4skin/view/components/rights/template.twig
 */
class Rights extends AbstractComponent
{
    /**
     * @var Root
     */
    protected $object;

    /**
     * @param Root $object
     */
    public function __construct(Root $object)
    {
        parent::__construct();

        $this->object = $object;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $data = [
            "rights" => $this->getRights(),
            "headerRows" => $this->getHeaderRows(),
            "groups" => $this->getGroupsForObject(),
        ];

        return $this->renderTemplate($data);
    }

    /**
     * @return array
     */
    private function getRights()
    {
        return \Kajona\System\System\Rights::getInstance()->getArrayRights($this->object->getSystemid());
    }

    /**
     * @return array
     */
    private function getGroupsForObject()
    {
        $rights = $this->getRights();

        $allGroupsIds = [];
        foreach ($rights as $right => $groupIds) {
            if (is_array($groupIds)) {
                $allGroupsIds = array_merge($allGroupsIds, $groupIds);
            }
        }

        $allGroupsIds = array_unique($allGroupsIds);

        // filter out admin group if not admin user
        $allGroupsIds = array_filter($allGroupsIds, function($groupId){
            $adminGroupId = SystemSetting::getConfigValue("_admins_group_id_");
            if ($groupId == $adminGroupId) {
                return in_array($adminGroupId, Session::getInstance()->getGroupIdsAsArray());
            } else {
                return true;
            }
        });

        return array_map(function($groupId){
            return Objectfactory::getInstance()->getObject($groupId);
        }, $allGroupsIds);
    }

    /**
     * @return array
     */
    private function getHeaderRows()
    {
        $lang = Lang::getInstance();

        //Load the rights header-row
        if ($this->object->getIntModuleNr() == 0) {
            $module = "system";
        } elseif ($this->object instanceof SystemModule) {
            $module = $this->object->getStrName();
        } else {
            $module = $this->object->getArrModule("modul");
        }

        if ($this->object instanceof SystemModule) {
            //try to find a module base header
            $headerRow = $lang->getLang("permissions_header_module", $module);
            if ($headerRow == "!permissions_header_module!") {
                $headerRow = $lang->getLang("permissions_header", $module);
            }
        } else {
            $headerRow = $lang->getLang("permissions_header", $module);
        }

        if ($headerRow == "!permissions_header!") {
            $headerRow = $lang->getLang("permissions_default_header", "system");
        }

        $headerTitles = $headerRow;

        if (!isset($headerTitles[9])) {
            $headerTitles[9] = $lang->getLang("permissions_default_header", "system")[9];
        }

        $headers = [
            \Kajona\System\System\Rights::$STR_RIGHT_VIEW => $headerTitles[0],
            \Kajona\System\System\Rights::$STR_RIGHT_EDIT => $headerTitles[1],
            \Kajona\System\System\Rights::$STR_RIGHT_DELETE => $headerTitles[2],
            \Kajona\System\System\Rights::$STR_RIGHT_RIGHT => $headerTitles[3],
            \Kajona\System\System\Rights::$STR_RIGHT_RIGHT1 => $headerTitles[4],
            \Kajona\System\System\Rights::$STR_RIGHT_RIGHT2 => $headerTitles[5],
            \Kajona\System\System\Rights::$STR_RIGHT_RIGHT3 => $headerTitles[6],
            \Kajona\System\System\Rights::$STR_RIGHT_RIGHT4 => $headerTitles[7],
            \Kajona\System\System\Rights::$STR_RIGHT_RIGHT5 => $headerTitles[8],
            \Kajona\System\System\Rights::$STR_RIGHT_CHANGELOG => $headerTitles[9],
        ];

        // remove empty header values in case the right is not configured
        $headers = array_filter($headers);

        return $headers;
    }
}
