<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Search;

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
     * @var ParserInterface[]
     */
    protected $arrParsers = [];

    public function __construct()
    {
        $this->addParser('xls', new Parser\Excel());
        $this->addParser('xlsx', new Parser\Excel());
        $this->addParser('ods', new Parser\Excel());
        $this->addParser('pdf', new Parser\PDF());
        $this->addParser('txt', new Parser\PlainText());
        $this->addParser('csv', new Parser\PlainText());
        $this->addParser('html', new Parser\PlainText());
        $this->addParser('ppt', new Parser\PowerPoint());
        $this->addParser('pptx', new Parser\PowerPoint());
        $this->addParser('odp', new Parser\PowerPoint());
        $this->addParser('doc', new Parser\Word());
        $this->addParser('docx', new Parser\Word());
        $this->addParser('odt', new Parser\Word());
        $this->addParser('rtf', new Parser\Word());
        $this->addParser('xml', new Parser\Xml());
    }

    /**
     * @param string $strFileExtension
     * @param ParserInterface $objParser
     */
    public function addParser($strFileExtension, ParserInterface $objParser)
    {
        $this->arrParsers[$strFileExtension] = $objParser;
    }

    /**
     * @param string $strPath
     * @return string
     */
    public function get($strPath)
    {
        $strFileExtension = pathinfo($strPath, PATHINFO_EXTENSION);

        if (isset($this->arrParsers[$strFileExtension])) {
            try {
                return $this->arrParsers[$strFileExtension]->getText($strPath);
            } catch (\Throwable $objE) {
                // could not parse file
            }
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
        $arrFiles = MediamanagerFile::getObjectListFiltered($objFilter, $objRepo->getSystemid());

        foreach ($arrFiles as $objFile) {
            /** @var MediamanagerFile $objFile */
            $strPath = $objFile->getStrFilename();
            $strContent = $this->get($strPath);

            if (!empty($strContent)) {
                $objFile->setStrSearchContent($strContent);
                $objFile->updateObjectToDb();
            }
        }
    }
}
