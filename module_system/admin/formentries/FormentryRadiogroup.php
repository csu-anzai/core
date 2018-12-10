<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\DropdownLoaderInterface;
use Kajona\System\System\Link;
use Kajona\System\System\Reflection;
use Kajona\System\System\ServiceProvider;
use Kajona\System\System\StringUtil;
use Kajona\System\System\Validators\TextValidator;


/**
 * A yes-no field renders a dropdown containing a list of entries.
 * Make sure to pass the list of possible entries before rendering the form.
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 */
class FormentryRadiogroup extends FormentryDropdown implements FormentryPrintableInterface
{

    /**
     * Renders the field itself.
     * In most cases, based on the current toolkit.
     *
     * @return string
     */
    public function renderField()
    {
        $objToolkit = Carrier::getInstance()->getObjToolkit("admin");
        $strReturn = "";
        if ($this->getStrHint() != null) {
            $strReturn .= $objToolkit->formTextHint($this->getStrHint(), $this->getBitHideLongHints());
        }

        $strReturn .= $objToolkit->formInputRadiogroup($this->getStrEntryName(), $this->getArrKeyValues(), $this->getStrLabel(), $this->getStrValue(), "", !$this->getBitReadonly());
        return $strReturn;
    }
}
