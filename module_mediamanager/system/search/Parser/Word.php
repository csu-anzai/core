<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Search\Parser;

use Kajona\Mediamanager\System\Search\ParserInterface;
use PhpOffice\PhpWord\Element;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\IOFactory;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
class Word implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        require_once __DIR__ . "/../../../vendor/autoload.php";

        $strFileExtension = pathinfo($strFile, PATHINFO_EXTENSION);
        $strReaderName = $this->getReaderName($strFileExtension);
        $objWord = IOFactory::load($strFile, $strReaderName);

        $arrSections = $objWord->getSections();
        $strContent = "";
        foreach ($arrSections as $objSection) {
            $strContent.= $this->walkElement($objSection) . " ";
        }

        return PlainText::normalize($strContent);
    }

    private function walkElement(AbstractElement $objElement)
    {
        if ($objElement instanceof AbstractContainer) {
            return $this->walkContainer($objElement);
        } elseif ($objElement instanceof Element\Text) {
            return trim($objElement->getText());
        } elseif ($objElement instanceof Element\ListItem) {
            return trim($objElement->getText());
        } elseif ($objElement instanceof Element\Title) {
            return trim($objElement->getText());
        } elseif ($objElement instanceof Element\Link) {
            return trim($objElement->getText());
        } elseif ($objElement instanceof Element\PreserveText) {
            return trim($objElement->getText());
        } elseif ($objElement instanceof Element\Table) {
            return $this->walkTable($objElement);
        }

        return null;
    }

    private function walkContainer(AbstractContainer $objContainer)
    {
        $arrElements = $objContainer->getElements();
        $arrReturn = [];

        foreach ($arrElements as $objElement) {
            $arrReturn[] = $this->walkElement($objElement);
        }

        return implode(" ", array_filter($arrReturn));
    }

    private function walkTable(Element\Table $objElement)
    {
        $arrRows = $objElement->getRows();
        $arrReturn = [];

        foreach ($arrRows as $objRow) {
            $arrCells = $objRow->getCells();
            foreach ($arrCells as $objCell) {
                $arrReturn[] = $this->walkElement($objCell);
            }
        }

        return implode(" ", array_filter($arrReturn));
    }

    /**
     * @param string $strFileExtension
     * @return string|null
     */
    private function getReaderName($strFileExtension)
    {
        switch (strtolower($strFileExtension)) {
            case 'doc':
                return 'MsDoc';

            case 'docx':
                return 'Word2007';
                break;

            case 'rtf':
                return 'RTF';
                break;

            case 'odt':
                return 'ODText';
                break;

            default:
                return null;
        }
    }
}
