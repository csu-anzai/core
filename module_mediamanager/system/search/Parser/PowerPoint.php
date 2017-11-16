<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Search\Parser;

use Kajona\Mediamanager\System\Search\ParserInterface;
use PhpOffice\PhpPresentation\AbstractShape;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape;
use PhpOffice\PhpPresentation\ShapeContainerInterface;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
class PowerPoint implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        require_once __DIR__ . "/../../../vendor/autoload.php";

        $strFileExtension = pathinfo($strFile, PATHINFO_EXTENSION);
        $pptReader = IOFactory::createReader($this->getReaderName($strFileExtension));
        $objPresentation = $pptReader->load($strFile);

        $strContent = "";
        $arrSlides = $objPresentation->getAllSlides();

        foreach ($arrSlides as $objSlide) {
            $strContent.= $this->walkContainer($objSlide) . " ";
        }

        return PlainText::normalize($strContent);
    }

    private function walkShape(AbstractShape $objShape)
    {
        if ($objShape instanceof ShapeContainerInterface) {
            return $this->walkContainer($objShape);
        } elseif ($objShape instanceof Shape\RichText) {
            return $objShape->getPlainText();
        } elseif ($objShape instanceof Shape\Comment) {
            return $objShape->getText();
        } elseif ($objShape instanceof Shape\Table) {
            return $this->walkTable($objShape);
        }

        return "";
    }

    private function walkContainer(ShapeContainerInterface $objContainer)
    {
        $strContent = "";
        $arrCollection = $objContainer->getShapeCollection();

        foreach ($arrCollection as $objShape) {
            $strContent.= $this->walkShape($objShape) . " ";
        }

        return $strContent;
    }

    private function walkTable(Shape\Table $objTable)
    {
        $arrRows = $objTable->getRows();
        $strContent = "";

        foreach ($arrRows as $objRow) {
            $arrCells = $objRow->getCells();
            foreach ($arrCells as $objCell) {
                $strContent.= $objCell->getPlainText() . " ";
            }
        }

        return $strContent;
    }

    /**
     * @param string $strFileExtension
     * @return string|null
     */
    private function getReaderName($strFileExtension)
    {
        switch (strtolower($strFileExtension)) {
            case 'ppt':
                return 'PowerPoint97';

            case 'pptx':
                return 'PowerPoint2007';
                break;

            case 'odp':
                return 'ODPresentation';
                break;

            default:
                return null;
        }
    }
}
