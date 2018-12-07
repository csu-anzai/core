<?php

declare(strict_types=1);

namespace Kajona\Tinyurl\Tests;

use Kajona\System\Tests\Testbase;
use Kajona\Tinyurl\System\TinyUrl;

class TinyurlTest extends Testbase
{

    public function testGetShortUrlAndLoadUrl()
    {
        $strLongUrl = "https://localhost/agp-core-project/#/riskanalysis/listRiskAnalysisTmplate/b2c76445b8e7cea40faa?folderview=%5B0%2C0%5D&contentfilter_session=riskanalysistemplatefilter&riskanalysistemplatefilter_title=abcd&riskanalysistemplatefilter_description=&riskanalysistemplatefilter_riskcategory%5B%5D=0&riskanalysistemplatefilter_riskcategory%5B%5D=3&riskanalysistemplatefilter_riskcategory%5B%5D=6&riskanalysistemplatefilter_createdatefrom=28.09.2018&riskanalysistemplatefilter_createdateto=06.10.2018&riskanalysistemplatefilter_status%5B%5D=0&riskanalysistemplatefilter_deploykey=bait_de&riskanalysistemplatefilter_setcontentfilter=true&formsent_agp%5Criskanalysis%5Csystem%5Cfilters%5Criskanalysistemplatefilter=1&pe=1&folderview=%5B0%2C0%5D&pv=1";
        $objTinyUrl = new TinyUrl();
        $strShortUrl = $objTinyUrl->getShortUrl($strLongUrl);
        $urlPieces = explode('#', $strShortUrl);
        $urlParams = explode('/', $urlPieces[1]);
        $this->assertEquals("tinyurl", $urlParams[1]);
        $this->assertEquals("loadUrl", $urlParams[2]);
        $this->assertTrue(validateSystemid($urlParams[3]));

        $urlId = $urlParams[3];
        $strLoadedUrl = $objTinyUrl->loadUrl($urlId);
        $this->assertEquals($strLoadedUrl, $strLongUrl);
    }

}