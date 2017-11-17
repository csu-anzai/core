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
            $strPath = _realpath_."/".$objFile->getStrFilename();
            $strContent = $this->get($strPath);

            if (!empty($strContent)) {
                $objFile->setStrSearchContent($strContent);
                $objFile->updateObjectToDb();
            }
        }
    }
}
