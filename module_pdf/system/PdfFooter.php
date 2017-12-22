<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\Pdf\System;


/**
 * Sample implementation of a footer.
 *
 * @author sidler
 * @package module_pdf
 * @since 3.3.0
 */
class PdfFooter implements PdfFooterInterface
{

    private $strFooterAddon = "";

    private $bitSkipFirstPage = false;


    /**
     * Writes the footer for a single page.
     * Use the passed $objPdf to access the pdf.
     *
     * @param PdfTcpdf $objPdf
     * @return void
     */
    public function writeFooter($objPdf)
    {

        if ($this->bitSkipFirstPage && $objPdf->getPage() == 1) {
            return;
        }

        // Position at 1.5 cm from bottom
        $objPdf->SetY(-10);
        // Set font
        $objPdf->SetFont('helvetica', 'I', 8);
        // Page number
        $objPdf->Cell(0, 0, $objPdf->getAliasNumPage().'/'.$objPdf->getAliasNbPages(), 'T', 0, 'R');

        $objPdf->SetY(-10);

        //date
        $objPdf->Cell(0, 0, ''.timeToString(time(), false).$this->strFooterAddon, '0', 0, 'L');

    }

    /**
     * @param string $strFooterAddon
     * @return void
     */
    public function setStrFooterAddon($strFooterAddon)
    {
        $this->strFooterAddon = $strFooterAddon;
    }

    /**
     * @return string
     */
    public function getStrFooterAddon()
    {
        return $this->strFooterAddon;
    }

    /**
     * @return bool
     */
    public function getBitSkipFirstPage(): bool
    {
        return $this->bitSkipFirstPage;
    }

    /**
     * @param bool $bitSkipFirstPage
     */
    public function setBitSkipFirstPage(bool $bitSkipFirstPage)
    {
        $this->bitSkipFirstPage = $bitSkipFirstPage;
    }


}
