<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                        *
********************************************************************************************************/

namespace Kajona\Dashboard\System;

use Kajona\System\Admin\AdminSimple;
use Kajona\System\System\AdminListableInterface;
use Kajona\System\System\Root;
use Kajona\System\System\SystemModule;

/**
 * Object which represents a concrete model instance to be added to the todo-list
 *
 * @author stefam.idler@artemeon.de
 * @since 7.0
 */
class ObjectTodoEntry extends TodoEntry
{
    /**
     * @var AdminListableInterface|Root
     */
    private $object = null;


    /**
     * ObjectTodoEntry constructor.
     * @param AdminListableInterface $object
     */
    public function __construct(AdminListableInterface $object)
    {
        $this->object = $object;
    }

    /**
     * @return array|string
     */
    public function getArrModuleNavi()
    {
        //call the original module to render the action-icons
        $objAdminInstance = SystemModule::getModuleByName($this->object->getArrModule("modul"))->getAdminInstanceOfConcreteModule();
        if ($objAdminInstance != null && $objAdminInstance instanceof AdminSimple) {
            return $objAdminInstance->getActionIcons($this->object);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getStrIcon()
    {
        return $this->object->getStrIcon();
    }

    /**
     * @inheritDoc
     */
    public function getStrDisplayName()
    {
        return $this->object->getStrDisplayName();
    }
}
