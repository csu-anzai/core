<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Fileindexer\System;

use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\Mediamanager\System\MediamanagerFileFilter;
use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\System\System\CoreEventdispatcher;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Psr\Log\LoggerInterface;

/**
 * The indexer is responsible to extract text content from different file formats i.e. pdf or docx. The default parser
 * uses the "tika" java app but it is also possible to provide another parser
 *
 * @package module_fileindexer
 * @author christoph.kappestein@artemeon.de
 */
class Indexer
{
    /**
     * @var int
     */
    const MAX_INDEX_COUNT = 16;

    /**
     * @var ParserInterface
     */
    protected $objParser;

    /**
     * @var LoggerInterface
     */
    protected $objLogger;

    /**
     * @param ParserInterface $objParser
     * @param LoggerInterface|null $objLogger
     */
    public function __construct(ParserInterface $objParser, LoggerInterface $objLogger = null)
    {
        $this->objParser = $objParser;
        $this->objLogger = $objLogger;
    }

    /**
     * Parses the first n files from the repository which are not indexed yet and extracts for each file the text
     * content and writes the text into the "strSearchContent" property. n is specified through the constant
     * MAX_INDEX_COUNT
     *
     * @param MediamanagerRepo $objRepo
     */
    public function index(MediamanagerRepo $objRepo)
    {
        $objFilter = new MediamanagerFileFilter();
        $objFilter->setBitIndexPending(true);
        $objFilter->setIntFileType(MediamanagerFile::$INT_TYPE_FILE);
        $objFilter->setStrFilename($objRepo->getStrPath());
        $arrFiles = MediamanagerFile::getObjectListFiltered($objFilter, "", 0, self::MAX_INDEX_COUNT);
        $arrResult = [];

        foreach ($arrFiles as $objFile) {
            /** @var MediamanagerFile $objFile */
            $strPath = realpath(_realpath_."/".$objFile->getStrFilename());
            if (!empty($strPath)) {
                $strContent = $this->get($strPath);
                $strContent = trim($strContent);

                if (!empty($strContent)) {
                    /* TODO: tbd: do we need to run it against the analyzer?
                    $objAnalyzer = new SearchStandardAnalyzer();
                    $objAnalyzer->analyze($strContent);

                    $arrResults = $objAnalyzer->getResults();
                    $strContent = implode(" ", array_keys($arrResults));
                    */
                    $objFile->setStrSearchContent($strContent);
                    ServiceLifeCycleFactory::getLifeCycle(get_class($objFile))->update($objFile);

                    $arrResult[] = $objFile;
                } else {
                    // we need to mark that we have scanned the file
                    $objFile->setStrSearchContent("-");
                    ServiceLifeCycleFactory::getLifeCycle(get_class($objFile))->update($objFile);
                }
            }
        }

        if (count($arrResult) > 0) {
            // fire event after repo was indexed
            CoreEventdispatcher::getInstance()->notifyGenericListeners(FileIndexerEventIdentifier::EVENT_FILEINDEXER_INDEX_COMPLETED, [$objRepo, $arrResult]);
        }
    }

    /**
     * Tries to parse the content of the provided file path and returns the content as plain text or null in case the
     * parser throws an exception
     *
     * @param string $strPath
     * @return string
     */
    private function get($strPath)
    {
        try {
            return $this->objParser->getText($strPath);
        } catch (\Throwable $objE) {
            // could not parse file
            if ($this->objLogger !== null) {
                $this->objLogger->error($objE->getMessage());
            }
        }

        return null;
    }
}
