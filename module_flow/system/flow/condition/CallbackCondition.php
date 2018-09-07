<?php
/*"******************************************************************************************************
*   (c) 2010-2017 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System\Flow\Condition;

use Kajona\Flow\System\FlowConditionInterface;
use Kajona\Flow\System\FlowConditionResult;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;

/**
 * A condition mainly used for testing
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class CallbackCondition implements FlowConditionInterface
{
    private $callback;

    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return self::class;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return "";
    }

    /**
     * Uses the first sub condition and negates the result
     *
     * @param Model $object
     * @param FlowTransition $transition
     * @return FlowConditionResult
     */
    public function validateCondition(Model $object, FlowTransition $transition)
    {
        return call_user_func_array($this->callback, [$object, $transition]);
    }

    /**
     * @inheritdoc
     */
    public function configureForm(AdminFormgenerator $form)
    {
    }
}

