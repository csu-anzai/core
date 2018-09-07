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
 * Abstract meta condition which can be used to implement logic meta conditions. The two conditions below this condition
 * are used i.e.:
 *
 * - LogicConditionAbstract
 *   - Condition (Left)
 *   - Condition (Right)
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
abstract class LogicConditionAbstract extends FlowConditionAbstract
{
    /**
     * Uses the first and second sub condition and calls the abstract evaluate method which does a logic operation
     *
     * @param Model $objObject
     * @param FlowTransition $objTransition
     * @return FlowConditionResult
     */
    public function validateCondition(Model $objObject, FlowTransition $objTransition)
    {
        $conditions = FlowConditionAbstract::getObjectListFiltered(null, $this->getSystemid());
        $leftCondition = array_shift($conditions);
        $rightCondition = array_shift($conditions);

        if ($leftCondition instanceof FlowConditionAbstract && $rightCondition instanceof FlowConditionAbstract) {
            return $this->evaluate(
                $leftCondition->validateCondition($objObject, $objTransition),
                $rightCondition->validateCondition($objObject, $objTransition)
            );
        }

        return new FlowConditionResult(true);
    }

    /**
     * @inheritdoc
     */
    public function configureForm(AdminFormgenerator $objForm)
    {
    }

    /**
     * @param FlowConditionResult $left
     * @param FlowConditionResult $right
     * @return FlowConditionResult
     */
    abstract protected function evaluate(FlowConditionResult $left, FlowConditionResult $right);
}

