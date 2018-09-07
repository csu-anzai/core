<?php
/*"******************************************************************************************************
*   (c) 2010-2017 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System\Flow\Condition;

use Kajona\Flow\System\FlowConditionResult;

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
    protected function evaluate(FlowConditionResult $left, FlowConditionResult $right)
    {
        $errors = array_merge($left->getErrors(), $right->getErrors());
        $menuItems = array_merge($left->getMenuItems(), $right->getMenuItems());

        return new FlowConditionResult($left->isValid() && $right->isValid(), $errors, $menuItems);
    }
}

