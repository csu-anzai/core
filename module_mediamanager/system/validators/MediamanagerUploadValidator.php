<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\Mediamanager\System\Validators;

use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\ValidatorInterface;

/**
 * A simple validator to validate a systemid.
 *
 * @author sidler@mulchprod.de
 * @since 6.5
 */
class MediamanagerUploadValidator implements ValidatorInterface
{
    private $strRepoId = "";
    private $bitMandatory = false;

    /**
     * MediamanagerUploadValidator constructor.
     * @param string $strRepoId
     * @param $bitMandatory
     */
    public function __construct($strRepoId, $bitMandatory)
    {
        $this->strRepoId = $strRepoId;
        $this->bitMandatory = $bitMandatory;
    }


    /**
     * Validates if there is at least a single file uploaded.
     * The value itself is the target dir right here.
     *
     * @param string $objValue
     * @return bool
     */
    public function validate($objValue)
    {
        if (!$this->bitMandatory) {
            return true;
        }

        /** @var MediamanagerRepo $objRepo */
        $objRepo = Objectfactory::getInstance()->getObject($this->strRepoId);
        $objMMFile = MediamanagerFile::getFileForPath($this->strRepoId, $objRepo->getStrPath()."/".$objValue);

        if ($objMMFile != null) {
            return MediamanagerFile::getObjectCountFiltered(null, $objMMFile->getSystemid()) > 0;
        }

        return false;
    }


}
