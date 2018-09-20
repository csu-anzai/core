<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Tinyurl\System;


use Kajona\System\System\Carrier;
use Kajona\System\System\Link;

/**
 * Class for creating a shortened URL
 *
 * @package module_tinyurl
 * @author andrii.konoval
 *
 */
class TinyUrl
{
    /**
     * Creates a shortened URL
     * @param string $strLongUrl
     * @return string
     */
    public static function getShortUrl($strLongUrl)
    {

        $strUrlId = generateSystemid();
        $objDb = Carrier::getInstance()->getObjDB();

        $strQuery = "SELECT url_id FROM agp_tinyurl WHERE url=?";
        $arrRows = $objDb->getPRow($strQuery, [$strLongUrl]);
        if (!empty($arrRows)) {
            return Link::getLinkAdminHref("tinyurl", "loadUrl", "&systemid=".$arrRows['url_id']);
        }

        if ($objDb->_pQuery("INSERT INTO agp_tinyurl (url_id, url) VALUES (?,?)", [$strUrlId, $strLongUrl])) {
            return Link::getLinkAdminHref("tinyurl", "loadUrl", "&systemid=".$strUrlId);
        }

        return "";
    }


    /**
     * Tries to load and post an url from the backend
     */
    public function loadUrl($strUrlId)
    {
        $strReturn = "";
        if (validateSystemid($strUrlId)) {
            $strQuery = "SELECT url FROM agp_tinyurl WHERE url_id=?";
            $arrRow = Carrier::getInstance()->getObjDB()->getPRow($strQuery, [$strUrlId]);
            if (!empty($arrRow)) {
                $strReturn = html_entity_decode($arrRow['url']);
            }
        }

        return $strReturn;
    }

}
