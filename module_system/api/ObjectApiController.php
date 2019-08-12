<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\Api;

use Kajona\Api\System\ApiControllerInterface;
use Kajona\Api\System\Http\JsonResponse;
use Kajona\System\Admin\AdminFormgeneratorFactory;
use Kajona\System\Admin\Exceptions\ModelNotFoundException;
use Kajona\System\System\ArraySectionIterator;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\OrmObjectlist;
use Kajona\System\System\Root;
use Kajona\System\System\SystemModule;
use PSX\Http\Environment\HttpContextInterface;

/**
 * A general API endpoint which can be used to get the form of a specific model and also to CRUD objects
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.2
 */
class ObjectApiController implements ApiControllerInterface
{
    /**
     * @inject system_object_factory
     * @var Objectfactory
     */
    private $objectFactory;

    /**
     * @inject system_life_cycle_factory
     * @var ServiceLifeCycleFactory
     */
    private $lifeCycleFactory;

    /**
     * Returns a form for a specific model
     *
     * @api
     * @method GET
     * @path /v1/system/forms/{systemid}
     * @authorization usertoken
     */
    public function getForm(HttpContextInterface $context): JsonResponse
    {
        $systemId = $context->getUriFragment('systemid');
        if (validateSystemid($systemId)) {
            $model = $this->getModelBySystemId($systemId);
        } else {
            $model = $this->getModelByClass($context->getParameter('class'));
        }

        $form = AdminFormgeneratorFactory::createByModel($model);

        return new JsonResponse([
            'form' => $form,
        ]);
    }

    /**
     * Lists all objects of a specific type below a previd
     *
     * @api
     * @method GET
     * @path /v1/system/objects
     * @authorization usertoken
     */
    public function listObjects(HttpContextInterface $context): JsonResponse
    {
        $model = $this->getModelByClass($context->getParameter('class'));
        $previd = $context->getParameter('previd');

        $orm = new OrmObjectlist();
        // @TODO handle filter

        $iterator = new ArraySectionIterator($orm->getObjectCount(get_class($model), $previd));
        $iterator->setPageNumber((int)($context->getParameter('pv') != '' ? $context->getParameter('pv') : 1));
        $iterator->setArraySection($orm->getObjectList(get_class($model), $previd, $iterator->calculateStartPos(), $iterator->calculateEndPos()));

        return new JsonResponse($iterator);
    }

    /**
     * Returns a single object
     *
     * @api
     * @method GET
     * @path /v1/system/objects/{systemid}
     * @authorization usertoken
     */
    public function detailObject(HttpContextInterface $context): JsonResponse
    {
        $systemId = $context->getUriFragment('systemid');
        $object = $this->objectFactory->getObject($systemId);

        if (!$object instanceof ModelInterface) {
            throw new ModelNotFoundException('Model not available');
        }

        if (!$object->rightView()) {
            throw new \RuntimeException('No rights to view the object');
        }

        return new JsonResponse($object);
    }

    /**
     * Creates a new object under the provided previd
     *
     * @api
     * @method POST
     * @path /v1/system/objects
     * @authorization usertoken
     */
    public function createObject(HttpContextInterface $context): JsonResponse
    {
        $model = $this->getModelByClass($context->getParameter('class'));

        // determine object parent
        $prevId = $context->getParameter('previd');
        if (validateSystemid($prevId)) {
            $parent = $this->getModelBySystemId($prevId);
        } else {
            $parent = SystemModule::getModuleByName($model->getArrModule('module'));
        }

        if (!$parent->rightEdit()) {
            throw new \RuntimeException('No rights to create an object');
        }

        $form = AdminFormgeneratorFactory::createByModel($model);

        if (!$form->validateForm()) {
            return new JsonResponse($form->getValidationErrors(), 400);
        }

        $form->updateSourceObject();

        $this->lifeCycleFactory->factory(get_class($model))->update($model, $parent->getSystemid());

        return new JsonResponse([
            'success' => true,
            'systemid' => $model->getSystemid(),
        ]);
    }

    /**
     * Updates a specific object
     *
     * @api
     * @method PUT
     * @path /v1/system/objects/{systemid}
     * @authorization usertoken
     */
    public function updateObject(HttpContextInterface $context): JsonResponse
    {
        $model = $this->getModelBySystemId($context->getParameter('systemid'));

        if (!$model->rightEdit()) {
            throw new \RuntimeException('No rights to edit the object');
        }

        $form = AdminFormgeneratorFactory::createByModel($model);

        if (!$form->validateForm()) {
            return new JsonResponse($form->getValidationErrors(), 400);
        }

        $form->updateSourceObject();

        $this->lifeCycleFactory->factory(get_class($model))->update($model);

        return new JsonResponse([
            'success' => true,
            'systemid' => $model->getSystemid(),
        ]);
    }

    /**
     * Deletes a specific object
     *
     * @api
     * @method DELETE
     * @path /v1/system/objects/{systemid}
     * @authorization usertoken
     */
    public function deleteObject(HttpContextInterface $context): JsonResponse
    {
        $model = $this->getModelBySystemId($context->getParameter('systemid'));

        if (!$model->rightDelete()) {
            throw new \RuntimeException('No rights to delete the object');
        }

        $this->lifeCycleFactory->factory(get_class($model))->delete($model);

        return new JsonResponse([
            'success' => true,
            'systemid' => $model->getSystemid(),
        ]);
    }

    private function getModelByClass(?string $class): Root
    {
        if (!class_exists($class)) {
            throw new \RuntimeException('Provided class does not exist');
        }

        $model = new $class();

        if (!$model instanceof Root) {
            throw new \RuntimeException('Provided class is no model');
        }

        return $model;
    }

    private function getModelBySystemId(?string $systemId): Root
    {
        if (!validateSystemid($systemId)) {
            throw new \RuntimeException('No valid systemid provided');
        }

        $model = $this->objectFactory->getObject($systemId);

        if (!$model instanceof Root) {
            throw new \RuntimeException('Provided systemid does not exist');
        }

        return $model;
    }
}

