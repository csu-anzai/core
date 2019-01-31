<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Dashobard\System\Lifecycle;

use Kajona\Dashboard\System\DashboardConfig;
use Kajona\Dashboard\System\DashboardUserRoot;
use Kajona\System\System\Carrier;
use Kajona\System\System\Lifecycle\ServiceLifeCycleImpl;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\OrmObjectlist;
use Kajona\System\System\Permissions\PermissionHandlerFactory;
use Kajona\System\System\Root;
use Kajona\System\System\Session;

/**
 * Messaging alert life cycle handler
 *
 * @author sidler@mulchprod.de
 * @since 7.1
 */
class ConfigLifecycle extends ServiceLifeCycleImpl
{
    private const CONFIG_DASHBOARD_SESSION_KEY = __CLASS__."dashboard_key";

    /** @var Session */
    private $session;


    /**
     * @inheritDoc
     */
    public function __construct(PermissionHandlerFactory $objPermissionFactory, Session $session)
    {
        $this->session = $session;
        parent::__construct($objPermissionFactory);
    }

    /**
     * @param DashboardConfig $objModel
     * @param bool $strPrevId
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function update(Root $objModel, $strPrevId = false)
    {

        parent::update($objModel, $strPrevId);

        if ($objModel->getBitDefault()) {
            //mark all other ones as non-default
            $orm = new OrmObjectlist();
            $ids = $orm->getObjectListIds(DashboardConfig::class, $objModel->getStrPrevId());
            $ids = array_filter($ids, function (string $id) use ($objModel){
                return $id != $objModel->getSystemid();
            });


            $placeholder = array_fill(0, count($ids), "?");
            $query = "UPDATE agp_dashboard_cfg SET cfg_default = 0 WHERE cfg_id IN (".implode(",", $placeholder).")";
            Carrier::getInstance()->getObjDB()->_pQuery($query, $ids);

        }
    }


    /**
     * Sets a given config active, based on the id of the config
     * @param string $id
     * @throws \Kajona\System\System\Exception
     */
    public function setActiveConfigId(string $id)
    {
        $this->session->setSession(self::CONFIG_DASHBOARD_SESSION_KEY, $id);
    }

    /**
     * Tries to load the currently active dashboard for the user
     * @param DashboardUserRoot $userRoot
     * @return DashboardConfig|null
     * @throws \Kajona\System\System\Exception
     */
    public function getActiveConfig(DashboardUserRoot $userRoot): ?DashboardConfig
    {
        $active = $this->session->getSession(self::CONFIG_DASHBOARD_SESSION_KEY);

        if (validateSystemid($active)) {
            $cfg = Objectfactory::getInstance()->getObject($active);
            if ($cfg instanceof DashboardConfig) {
                return $cfg;
            }
        }

        //fallback - load the list of possible entries
        /** @var DashboardConfig $cfg */
        foreach (DashboardConfig::getObjectListFiltered(null, $userRoot->getSystemid()) as $cfg) {
            $this->session->setSession(self::CONFIG_DASHBOARD_SESSION_KEY, $cfg->getSystemid());
            return $cfg;
        }

        return null;
    }

}
