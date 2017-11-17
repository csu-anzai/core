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
use Kajona\Search\System\SearchStandardAnalyzer;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
class Indexer
{
    /**
     * @var ParserInterface
     */
    protected $objParser;

    public function __construct(ParserInterface $objParser)
    {
        $this->objParser = $objParser;
    }

    /**
     * @param string $strPath
     * @return string
     */
    public function get($strPath)
    {
        try {
            return $this->objParser->getText($strPath);
        } catch (\Throwable $objE) {
            // could not parse file
        }

        return null;
    }

    /**
     * @param MediamanagerRepo $objRepo
     */
    public function index(MediamanagerRepo $objRepo)
    {
        $objFilter = new MediamanagerFileFilter();
        $objFilter->setBitIndexPending(true);
        $objFilter->setIntFileType(MediamanagerFile::$INT_TYPE_FILE);
        $arrFiles = MediamanagerFile::getObjectListFiltered($objFilter);

        foreach ($arrFiles as $objFile) {
            /** @var MediamanagerFile $objFile */
            $strPath = realpath(_realpath_."/".$objFile->getStrFilename());
            if (!empty($strPath)) {
                $strContent = $this->get($strPath);
                $strContent = trim($strContent);

                if (!empty($strContent)) {
                    $objAnalyzer = new SearchStandardAnalyzer();
                    $objAnalyzer->analyze($strContent);

                    $arrResults = $objAnalyzer->getResults();
                    $strContent = implode(" ", array_keys($arrResults));

                    $objFile->setStrSearchContent($strContent);
                    $objFile->updateObjectToDb();
                } else {
                    // we need to mark that we have scanned the file
                    $objFile->setStrSearchContent("-");
                    $objFile->updateObjectToDb();
                }
            }
        }
    }
}
