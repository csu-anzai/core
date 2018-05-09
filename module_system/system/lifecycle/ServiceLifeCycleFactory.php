<?php

namespace Kajona\System\System\Lifecycle;

use Kajona\System\System\Carrier;
use Kajona\System\System\Config;
use Kajona\System\System\Reflection;
use Kajona\System\System\Root;
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

    /**
     * Returns the fitting life cycle for this model class. Note if you are in a context where you can access the DI
     * container i.e. the controller it is recommended to use the @inject annotation. Use this method only in case you
     * have no other option to access the DI container. This method exists only so that we dont have to write this
     * boilerplate code very often and that we can easily find the places where we access the DI container from a global
     * context
     *
     * @deprecated
     * @param string|Root $model
     * @return ServiceLifeCycleInterface
     */
    public static function getLifeCycle($model)
    {
        if (is_string($model)) {
            $class = $model;
        } elseif ($model instanceof Root) {
            $class = get_class($model);
        } else {
            throw new \InvalidArgumentException("Model must be either a string or an instance of Root");
        }

        /** @var ServiceLifeCycleFactory $objFactory */
        return Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_LIFE_CYCLE_FACTORY)->factory($class);
    }
}
