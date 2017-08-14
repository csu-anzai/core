<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/
declare(strict_types=1);

namespace Kajona\System\System;

use Kajona\System\System\Lifecycle\ServiceLifeCycleImpl;

/**
 * Messaging alert life cycle handler
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 * @package module_messaging
 *
 * @module messaging
 * @moduleId _messaging_module_id_
 */
class MessagingAlertLifeCycle extends ServiceLifeCycleImpl
{
    /**
     * By default, deleting a message removes it from the db completely, no logical deletes.
     * @inheritDoc
     */
    public function delete(Root $objModel)
    {
        parent::deleteObjectFromDatabase($objModel);
    }
}
