<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Search;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
interface ParserInterface
{
    /**
     * Returns all text from the provided file
     *
     * @param string $strFile
     * @return string
     */
    public function getText($strFile);
}
