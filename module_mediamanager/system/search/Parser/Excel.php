<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Search\Parser;

use Kajona\Mediamanager\System\Search\ParserInterface;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 */
class Excel implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function getText($strFile)
    {
        require_once __DIR__ . "/../../../vendor/autoload.php";

        $strFileExtension = pathinfo($strFile, PATHINFO_EXTENSION);
        $objReader = \PHPExcel_IOFactory::createReader($this->getReaderName($strFileExtension));
        $objExcel = $objReader->load($strFile);

        $strContent = "";
        $arrSheets = $objExcel->getAllSheets();

        foreach ($arrSheets as $objSheet) {
            $arrColumns = $objSheet->getColumnIterator();
            $arrRows = $objSheet->getRowIterator();

            foreach ($arrRows as $objRow) {
                /** @var \PHPExcel_Worksheet_Row $objRow */
                foreach ($arrColumns as $objColumn) {
                    /** @var \PHPExcel_Worksheet_Column $objColumn */
                    $objCell = $objSheet->getCell($objColumn->getColumnIndex() . $objRow->getRowIndex());
                    $strValue = $objCell->getValue();

                    if (!empty($strValue) && is_string($strValue)) {
                        $strContent.= trim($strValue) . " ";
                    }
                }
            }
        }

        return PlainText::normalize($strContent);
    }

    /**
     * @param string $strFileExtension
     * @return string|null
     */
    private function getReaderName($strFileExtension)
    {
        switch (strtolower($strFileExtension)) {
            case 'xls':
                return 'Excel5';

            case 'xlsx':
                return 'Excel2007';
                //return 'Excel2003XML';
                break;

            case 'ods':
                return 'OOCalc';
                break;

            default:
                return null;
        }
    }
}
