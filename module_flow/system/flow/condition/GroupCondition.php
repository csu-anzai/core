<?php
/*"******************************************************************************************************
*   (c) 2010-2017 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Flow\System\Flow\Condition;

use Kajona\Flow\System\FlowConditionAbstract;
use Kajona\Flow\System\FlowConditionResult;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;

/**
 * A meta condition which can be used to validate multiple sub conditions. Therefor you only need to store the condition
 * below this condition. The condition will evaluate all sub conditions and return only true in case all sub conditions
 * are also true
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class GroupCondition extends FlowConditionAbstract
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getLang("flow_condition_group_title", "flow");
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getLang("flow_condition_group_description", "flow");
    }

    /**
     * Returns true in case all conditions under this condition are valid
     *
     * @param Model $objObject
     * @param FlowTransition $objTransition
     * @return FlowConditionResult
     */
    public function validateCondition(Model $objObject, FlowTransition $objTransition)
    {
        $conditions = FlowConditionAbstract::getObjectListFiltered(null, $this->getSystemid());

        return $this->validateConditions($conditions, $objObject, $objTransition);
    }

    /**
     * @inheritdoc
     */
    public function configureForm(AdminFormgenerator $objForm)
    {
    }

    /**
     * @param array $conditions
     * @param Model $objObject
     * @param FlowTransition $objTransition
     * @return FlowConditionResult
     */
    protected function validateConditions(array $conditions, Model $objObject, FlowTransition $objTransition)
    {
        $result = new FlowConditionResult();

        foreach ($conditions as $condition) {
            /** @var FlowConditionAbstract $condition */
            $result->merge($condition->validateCondition($objObject, $objTransition));
        }

        return $result;
    }
}
