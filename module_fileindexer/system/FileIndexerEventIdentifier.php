<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Fileindexer\System;

/**
 * @package module_fileindexer
 * @author christoph.kappestein@artemeon.de
 */
interface FileIndexerEventIdentifier
{
    /**
     * Invoked after a repository was indexed
     *
     * @param bool $bitAdmin
     * @param string $strModule
     * @param string $strAction
     * @param string $strLanguageParam
     */
    const EVENT_FILEINDEXER_INDEX_COMPLETED = "fileindexer.system.index.completed";
}
