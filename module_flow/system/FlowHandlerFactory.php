<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System;

use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;

/**
 * Factory class to create a flow handler instance
 *
 * @author christoph.kappestein@artemeon.de
 * @module flow
 */
class FlowHandlerFactory
{
    /**
     * @var FlowManager
     */
    protected $objFlowManager;

    /**
     * @var ServiceLifeCycleFactory
     */
    protected $objLifeCycleFactory;

    /**
     * @param FlowManager $objFlowManager
     * @param ServiceLifeCycleFactory $objLifeCycleFactory
     */
    public function __construct(FlowManager $objFlowManager, ServiceLifeCycleFactory $objLifeCycleFactory)
    {
        $this->objFlowManager = $objFlowManager;
        $this->objLifeCycleFactory = $objLifeCycleFactory;
    }

    /**
     * @param string $strClass
     * @return FlowHandlerInterface
     */
    public function factory($strClass)
    {
        if (class_exists($strClass)) {
            $objHandler = new $strClass($this->objFlowManager, $this->objLifeCycleFactory);

            if ($objHandler instanceof FlowHandlerInterface) {
                return $objHandler;
            } else {
                throw new \InvalidArgumentException("Provided flow handler class must be an instance of FlowHandlerInterface");
            }
        } else {
            throw new \InvalidArgumentException("Provided flow handler class does not exist");
        }
    }
}
