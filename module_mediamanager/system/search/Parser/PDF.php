<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Search\Parser;

use Kajona\Mediamanager\System\Search\ParserInterface;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
class PDF implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        require_once __DIR__ . "/../../../vendor/autoload.php";

        $objParser = new \Smalot\PdfParser\Parser();
        $objPdf = $objParser->parseFile($strFile);

        return PlainText::normalize($objPdf->getText());
    }
}
