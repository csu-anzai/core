<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*    $Id$                        *
********************************************************************************************************/

namespace Kajona\Mediamanager\Admin;

use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryTextarea;

/**
 * The formgenerator for a mediamanager repo
 *
 * @package module_mediamanager
 * @author sidler@mulchprod.de
 * @since 4.8
 *
 */
class MediamanagerFileFormgenerator extends AdminFormgenerator {
    /**
     * @inheritDoc
     */
    public function generateFieldsFromObject()
    {
        parent::generateFieldsFromObject();


        /** @var MediamanagerFile $objFile */
        $objFile = $this->getObjSourceobject();
        if ($objFile->getRepository()->getIntSearchIndex() == 1) {
            $this->addField(new FormentryTextarea("mediamanager", "strSearchContent"))
                ->setStrValue($objFile->getStrSearchContent())
                ->setBitReadonly(true)
                ->setStrLabel($this->getLang("form_mediamanager_content"));
        }
    }

}

