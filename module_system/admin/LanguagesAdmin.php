<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                              *
********************************************************************************************************/

namespace Kajona\System\Admin;

use Kajona\System\Admin\Formentries\FormentryHidden;
use Kajona\System\System\ArraySectionIterator;
use Kajona\System\System\Carrier;
use Kajona\System\System\Exception;
use Kajona\System\System\LanguagesLanguage;
use Kajona\System\System\Link;
use Kajona\System\System\Model;


/**
 * Admin-class to manage all languages
 *
 * @package module_languages
 * @author sidler@mulchprod.de
 *
 * @module languages
 * @moduleId _languages_modul_id_
 *
 * @objectList Kajona\System\System\LanguagesLanguage
 * @objectEdit Kajona\System\System\LanguagesLanguage
 * @objectNew Kajona\System\System\LanguagesLanguage
 */
class LanguagesAdmin extends AdminEvensimpler implements AdminInterface
{

    public function getOutputModuleNavi()
    {
        $arrReturn = array();
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "list", "", $this->getLang("commons_list"), "", "", true, "adminnavi"));
        return $arrReturn;
    }



    protected function renderCopyAction(Model $objListEntry)
    {
        return "";
    }


}
