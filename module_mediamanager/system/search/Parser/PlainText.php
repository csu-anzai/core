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
class PlainText implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        return self::normalize(file_get_contents($strFile));
    }

    /**
     * @param string $strContent
     * @return string
     */
    public static function normalize($strContent)
    {
        preg_match_all('/\w{2,}+/ims', $strContent, $arrMatches);

        return implode(" ", $arrMatches[0] ?? []);
    }
}
