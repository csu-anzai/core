<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\Flow\Admin;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryHeadline;

/**
 * Formgenerator for a flow entry
 *
 * @package module_flow
 * @author christoph.kappestein@gmail.com
 * @since 5.1
 */
class FlowStatusFormgenerator extends AdminFormgenerator
{
    /**
     * @inheritDoc
     */
    public function generateFieldsFromObject()
    {
        parent::generateFieldsFromObject();

        $this->addField(new FormentryHeadline("headline_roles"))
            ->setStrValue($this->getLang("form_flow_headline_roles"));
        $this->setFieldToPosition("headline_roles", 3);
    }
}
