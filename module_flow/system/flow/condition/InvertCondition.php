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
 * A meta condition which can be used to negate a specific condition
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class InvertCondition extends FlowConditionAbstract
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getLang("flow_condition_invert_title", "flow");
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getLang("flow_condition_invert_description", "flow");
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
        $conditions = FlowConditionAbstract::getObjectListFiltered(null, $this->getSystemid());
        $condition = array_shift($conditions);

        if ($condition instanceof FlowConditionAbstract) {
            $result = $condition->validateCondition($object, $transition);

            return new FlowConditionResult(!$result->isValid(), $result->getErrors(), $result->getMenuItems());
        }

        return new FlowConditionResult(true);
    }

    /**
     * @inheritdoc
     */
    public function configureForm(AdminFormgenerator $form)
    {
    }
}

