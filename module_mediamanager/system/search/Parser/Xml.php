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
class Xml implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        $objDocument = new \DOMDocument();
        $objDocument->load($strFile);

        $objXPath = new \DOMXPath($objDocument);
        $arrNodes = $objXPath->query('//text()');

        $strContent = "";
        foreach ($arrNodes as $objNode) {
            if ($objNode instanceof \DOMText) {
                $strContent.= $objNode->wholeText . "\n";
            }
        }

        return PlainText::normalize($strContent);
    }
}
