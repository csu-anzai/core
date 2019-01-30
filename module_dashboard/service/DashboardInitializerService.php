<?php
/*"******************************************************************************************************
*   (c) 2016 ARTEMEON                                                                              *
********************************************************************************************************/

namespace Kajona\Dashboard\Service;

use Kajona\Dashboard\Admin\Widgets\AdminwidgetNote;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetRssfeed;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetSystemcheck;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetSysteminfo;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetSystemlog;
use Kajona\Dashboard\Admin\Widgets\AdminwidgetTodo;
use Kajona\Dashboard\System\DashboardConfig;
use Kajona\Dashboard\System\DashboardUserRoot;
use Kajona\Dashboard\System\DashboardWidget;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemModule;

/**
 * A service creating the default dashboard for new users.
 *
 * @package Kajona\Dashboard\Service
 * @author sidler@mulchprod.de
 * @since 6.2
 */
class DashboardInitializerService
{

    /**
     * @param $strUserid
     *
     * @return bool
     * @throws \Kajona\System\System\Exception
     */
    public function createInitialDashboard($strUserid)
    {
        $userNode = DashboardUserRoot::getOrCreateForUser($strUserid);
        $cfg1 = new DashboardConfig();
        $cfg1->setStrTitle("Content");
        $cfg1->setBitDefault(true);
        ServiceLifeCycleFactory::getLifeCycle($cfg1)->update($cfg1, $userNode->getSystemid());

        $cfg2 = new DashboardConfig();
        $cfg2->setStrTitle("Management");
        ServiceLifeCycleFactory::getLifeCycle($cfg2)->update($cfg2, $userNode->getSystemid());


        $objDashboard = new DashboardWidget();
        $objDashboard->setStrColumn("column2");
        $objDashboard->setStrClass(AdminwidgetNote::class);
        $objDashboard->setStrContent(serialize(["content" => "Welcome to Kajona V6!<br /><br  />Kajona is developed by volunteers all over the world - show them your support by liking Kajona on facebook or donating a beer.
                    <div id=\"fb-root\"></div>
                    <script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) {return;} js = d.createElement(s); js.id = id; js.src = \"//connect.facebook.net/en_US/all.js#appId=141503865945925&xfbml=1\"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
                    <div class=\"fb-like\" data-href=\"https://www.facebook.com/pages/Kajona%C2%B3/156841314360532\" data-send=\"false\" data-layout=\"button_count\" data-width=\"60\" data-show-faces=\"false\"></div>
                    <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\"><input type=\"hidden\" name=\"cmd\" value=\"_donations\" /> <input type=\"hidden\" name=\"business\" value=\"donate@kajona.de\" /> 
                    <input type=\"hidden\" name=\"item_name\" value=\"Kajona Development\" /> <input type=\"hidden\" name=\"no_shipping\" value=\"0\" /> <input type=\"hidden\" name=\"no_note\" value=\"1\" /> 
                    <input type=\"hidden\" name=\"currency_code\" value=\"EUR\" /> <input type=\"hidden\" name=\"tax\" value=\"0\" /> <input type=\"hidden\" name=\"bn\" value=\"PP-DonationsBF\" /> 
                    <input type=\"image\" border=\"0\" src=\"https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\" /> 
                    <img height=\"1\" width=\"1\" border=\"0\" alt=\"\" src=\"https://www.paypal.com/en_US/i/scr/pixel.gif\" /></form>"]));
        ServiceLifeCycleFactory::getLifeCycle(get_class($objDashboard))->update($objDashboard, $cfg1->getSystemid());

        $objDashboard = new DashboardWidget();
        $objDashboard->setStrColumn("column3");
        $objDashboard->setStrClass(AdminwidgetTodo::class);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objDashboard))->update($objDashboard, $cfg1->getSystemid());

        $objDashboard = new DashboardWidget();
        $objDashboard->setStrColumn("column3");
        $objDashboard->setStrClass(AdminwidgetRssfeed::class);
        $objDashboard->setStrContent(serialize(["feedurl" => "http://www.kajona.de/kajona_news_en.rss", "posts" => "4"]));
        ServiceLifeCycleFactory::getLifeCycle(get_class($objDashboard))->update($objDashboard, $cfg1->getSystemid());



        $objDashboard = new DashboardWidget();
        $objDashboard->setStrColumn("column1");
        $objDashboard->setStrClass(AdminwidgetSystemcheck::class);
        $objDashboard->setStrContent(serialize(array("php" => "checked", "kajona" => "checked")));
        ServiceLifeCycleFactory::getLifeCycle(get_class($objDashboard))->update($objDashboard, $cfg1->getSystemid());

        $objDashboard = new DashboardWidget();
        $objDashboard->setStrColumn("column1");
        $objDashboard->setStrClass(AdminwidgetSysteminfo::class);
        $objDashboard->setStrContent(serialize(array("php" => "checked", "server" => "checked", "kajona" => "checked")));
        ServiceLifeCycleFactory::getLifeCycle(get_class($objDashboard))->update($objDashboard, $cfg1->getSystemid());

        $objDashboard = new DashboardWidget();
        $objDashboard->setStrColumn("column3");
        $objDashboard->setStrClass(AdminwidgetSystemlog::class);
        $objDashboard->setStrContent(serialize(array("nrofrows" => "1")));
        ServiceLifeCycleFactory::getLifeCycle(get_class($objDashboard))->update($objDashboard, $cfg1->getSystemid());



        return true;
    }
}
