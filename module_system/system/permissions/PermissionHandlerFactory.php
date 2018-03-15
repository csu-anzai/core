<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                        *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System\Permissions;

use Kajona\System\System\Exception;
use Kajona\System\System\Reflection;
use Pimple\Container;

/**
 * PermissionHandlerFactory
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class PermissionHandlerFactory
{
    /**
     * @var Container
     */
    protected $objContainer;

    /**
     * @param Container $objContainer
     */
    public function __construct(Container $objContainer)
    {
        $this->objContainer = $objContainer;
    }

    /**
     * @param string $strModelClass
     * @return PermissionHandlerInterface
     * @throws \RuntimeException
     * @throws Exception
     */
    public function factory($strModelClass)
    {
        // check whether the model has a permission handler
        $objReflection = new Reflection($strModelClass);
        $arrHandler = $objReflection->getAnnotationValuesFromClass(PermissionHandlerInterface::PERMISSION_HANDLER_ANNOTATION);

        if (!empty($arrHandler)) {
            $strHandler = reset($arrHandler);
            $objPermissionHandler = $this->objContainer->offsetGet($strHandler);

            if ($objPermissionHandler instanceof PermissionHandlerInterface) {
                return $objPermissionHandler;
            } else {
                throw new \RuntimeException(sprintf("Provided permission handler is not an instance of %s", PermissionHandlerInterface::class));
            }
        }

        return null;
    }
}
