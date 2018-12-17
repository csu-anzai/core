<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;

/**
 * Similar to a dropdown, but rendered as a list of radio buttons to force
 * the user to select a single value.
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 */
class FormentryRadiogroup extends FormentryDropdown implements FormentryPrintableInterface
{

    /**
     * @inheritdoc
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
