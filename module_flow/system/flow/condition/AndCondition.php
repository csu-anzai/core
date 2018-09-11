<?php
/*"******************************************************************************************************
*   (c) 2010-2017 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System\Flow\Condition;

use Kajona\Flow\System\FlowConditionInterface;
use Kajona\Flow\System\FlowConditionResult;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\System\Model;

/**
 * Meta condition which returns true in case the left and right condition is true
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class AndCondition extends LogicConditionAbstract
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getLang("flow_condition_and_title", "flow");
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getLang("flow_condition_and_description", "flow");
    }

    /**
     * @inheritdoc
     */
    protected function evaluate(FlowConditionInterface $left, FlowConditionInterface $right, Model $object, FlowTransition $transition)
    {
        $errors = [];
        $menuItems = [];

        $leftResult = $left->validateCondition($object, $transition);
        $errors = array_merge($errors, $leftResult->getErrors());
        $menuItems = array_merge($menuItems, $leftResult->getMenuItems());

        if (!$leftResult->isValid()) {
            // short-circuit evaluation in case the left condition is false we dont evaluate the right condition
            return new FlowConditionResult(false, $errors, $menuItems);
        }

        $rightResult = $right->validateCondition($object, $transition);
        $errors = array_merge($errors, $rightResult->getErrors());
        $menuItems = array_merge($menuItems, $rightResult->getMenuItems());

        return new FlowConditionResult($rightResult->isValid(), $errors, $menuItems);
    }
}

