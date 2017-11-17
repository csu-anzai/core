<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Fileindexer\System\Parser;

use Kajona\Fileindexer\System\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
class Tika implements ParserInterface
{
    protected $strCmd;

    public function __construct($strCmd)
    {
        $this->strCmd = $strCmd;
    }

    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        $objProcess = new Process($this->strCmd . " --text " . $strFile);
        $objProcess->run();

        if (!$objProcess->isSuccessful()) {
            throw new ProcessFailedException($objProcess);
        }

        return $objProcess->getOutput();
    }
}
