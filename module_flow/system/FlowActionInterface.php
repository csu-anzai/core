<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Flow\System;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;

/**
 * The action class is executed if a status transition happens. A status transition is always triggered by a user
 * interaction and not by an automatic event. Actions can be attached to a StatustransitionTransition object
 *
 * @author christoph.kappestein@artemeon.de
 * @author stefan.meyer@artemeon.de
 * @module flow
 */
interface FlowActionInterface
{
    const ORDER_PRE = 1;
    const ORDER_DEFAULT = 2;
    const ORDER_POST = 3;

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * By default every action is assigned to the default order which means there is no specified order. In most cases
     * it is recommended to develop actions which are independent of a specific order. The order can be of type PRE or
     * POST which guarantees that the action gets executed either before or after the normal actions. In case multiple
     * actions have a PRE/POST order there is no specified order within these actions, therefore it is recommended to
     * use this flag only if necessary
     *
     * @return int
     */
    public function getOrder();

    /**
     * Is called on a status change
     *
     * @param Model $objObject
     * @param FlowTransition $objTransition
     * @return void
     */
    public function executeAction(Model $objObject, FlowTransition $objTransition);

    /**
     * @param AdminFormgenerator $objForm
     * @param FlowTransition $objTransition
     * @return void
     * @internal param FlowTransition $objTransition
     */
    public function configureForm(AdminFormgenerator $objForm, FlowTransition $objTransition);
}
