<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Fileindexer\System\Parser;

use Kajona\Fileindexer\System\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Parser which uses the tika java app to extract text from a file
 * 
 * @see https://tika.apache.org/
 * @package module_fileindexer
 * @author christoph.kappestein@artemeon.de
 */
class Tika implements ParserInterface
{
    /**
     * @var string
     */
    protected $strJava;

    /**
     * @var string
     */
    protected $strTika;

    /**
     * @param string $strJava
     * @param string $strTika
     */
    public function __construct($strJava, $strTika)
    {
        $this->strJava = $strJava;
        $this->strTika = $strTika;
    }

    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        require_once __DIR__ . "/../../vendor/autoload.php";

        $objBuilder = new ProcessBuilder();
        $objBuilder->setPrefix($this->strJava);
        $objBuilder->add("-jar");
        $objBuilder->add($this->strTika);
        $objBuilder->add("--text");
        $objBuilder->add("--encoding=UTF-8");
        $objBuilder->add($strFile);

        $objProcess = $objBuilder->getProcess();
        $objProcess->run();

        if (!$objProcess->isSuccessful()) {
            throw new ProcessFailedException($objProcess);
        }

        return $objProcess->getOutput();
    }
}
