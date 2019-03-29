<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\Api;

use Kajona\Api\System\ApiControllerInterface;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Systemtasks\AdminSystemtaskInterface;
use Kajona\System\Admin\Systemtasks\SystemtaskBase;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Exception\NotFoundException;

/**
 * SystemTaskApiController
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class SystemTaskApiController implements ApiControllerInterface
{
    /**
     * Endpoint which returns all available system tasks
     *
     * @api
     * @method GET
     * @path /systemtask
     * @authorization filetoken
     */
    public function listSystemTasks()
    {
        $systemTasks = SystemtaskBase::getAllSystemtasks();
        $return = [];

        foreach ($systemTasks as $systemTask) {
            $group = $systemTask->getGroupIdentifier() ?: "default";

            if (!isset($return[$group])) {
                $return[$group] = [];
            }

            $return[$group][$systemTask->getStrInternalTaskName()] = $systemTask->getStrTaskName();
        }

        ksort($return);

        return [
            "systemtasks" => $return
        ];
    }

    /**
     * Returns the systemtask form
     *
     * @api
     * @method GET
     * @path /systemtask/{systemtask}
     * @authorization filetoken
     */
    public function getSystemTask(HttpContext $context)
    {
        $systemTask = $this->getSystemTaskByName($context->getUriFragment("systemtask"));
        if ($systemTask instanceof AdminSystemtaskInterface) {
            $form = $systemTask->getAdminForm();
            if ($form instanceof AdminFormgenerator) {
                $fields = $form->getArrFields();
            } else {
                $fields = null;
            }

            return [
                "form" => $fields,
            ];
        } else {
            throw new NotFoundException("System task not found");
        }
    }

    /**
     * Executes a specific systemtask
     *
     * @api
     * @method POST
     * @path /systemtask/{systemtask}
     * @authorization filetoken
     */
    public function executeSystemTask($body, HttpContext $context)
    {
        $systemTask = $this->getSystemTaskByName($context->getUriFragment("systemtask"));
        if ($systemTask instanceof AdminSystemtaskInterface) {
            // set request data
            $systemTask->setRequestData((array) $body);

            // execute the system task
            $return = $systemTask->executeTask();

            return [
                "success" => true,
                "return" => $return
            ];
        } else {
            throw new NotFoundException("System task not found");
        }
    }

    /**
     * @param string $name
     * @return AdminSystemtaskInterface|null
     */
    private function getSystemTaskByName(string $name)
    {
        $systemTasks = SystemtaskBase::getAllSystemtasks();
        foreach ($systemTasks as $systemTask) {
            if ($name == $systemTask->getStrInternalTaskName()) {
                return $systemTask;
            }
        }

        return null;
    }
}

