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
 * Meta condition which returns true in case either left or right is true but not both
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class XorCondition extends LogicConditionAbstract
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getLang("flow_condition_xor_title", "flow");
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getLang("flow_condition_xor_description", "flow");
    }

    /**
     * @inheritdoc
     */
    protected function evaluate(FlowConditionInterface $left, FlowConditionInterface $right, Model $object, FlowTransition $transition)
    {
        $leftResult = $left->validateCondition($object, $transition);
        $rightResult = $left->validateCondition($object, $transition);

        $errors = array_merge($leftResult->getErrors(), $rightResult->getErrors());
        $menuItems = array_merge($leftResult->getMenuItems(), $rightResult->getMenuItems());

        return new FlowConditionResult($leftResult->isValid() xor $rightResult->isValid(), $errors, $menuItems);
    }
}

