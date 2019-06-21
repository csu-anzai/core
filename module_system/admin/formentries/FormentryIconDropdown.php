<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

/**
 * Formentry to show dropdown with icons in option list
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 */
class FormentryIconDropdown extends FormentryDropdown
{
    public function __construct(string $formName, string $sourceProperty, object $sourceObject = null)
    {
        parent::__construct($formName, $sourceProperty, $sourceObject);

        $this->setIconValues(true);
    }

}
