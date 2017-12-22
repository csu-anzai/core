<?php

namespace Kajona\System\System\Lifecycle;

use Kajona\System\System\Config;
use Kajona\System\System\Reflection;
use Kajona\System\System\ServiceProvider;
use Pimple\Container;

/**
 * ServiceLifeCycleFactory
 *
 * @package Kajona\System\System
 * @author christoph.kappestein@gmail.com
 * @since 7.0
 */
class ServiceLifeCycleFactory
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
     * @param string $strClass
     * @return ServiceLifeCycleInterface
     */
    public function factory($strClass)
    {
        // check whether we have a project specific config to use a different life cycle service then
        // the one provide at the model annotation. Through this we can properly extend an existing service
        // or provide a complete new implementation
        $arrServices = Config::getInstance()->getConfig("service_lifecycle");
        if (!empty($arrServices) && is_array($arrServices)) {
            if (isset($arrServices[$strClass])) {
                return $this->objContainer->offsetGet($arrServices[$strClass]);
            }
        }

        $objReflection = new Reflection($strClass);
        $arrValues = $objReflection->getAnnotationValuesFromClass(ServiceLifeCycleInterface::STR_SERVICE_ANNOTATION);
        $strServiceName = reset($arrValues);

        if ($this->objContainer->offsetExists($strServiceName)) {
            return $this->objContainer->offsetGet($strServiceName);
        } else {
            return $this->objContainer->offsetGet(ServiceProvider::STR_LIFE_CYCLE_DEFAULT);
        }
    }
}
