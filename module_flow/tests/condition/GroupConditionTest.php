<?php

namespace Kajona\Flow\Tests\Condition;

use Kajona\Flow\System\Flow\Condition\CallbackCondition;
use Kajona\Flow\System\Flow\Condition\GroupCondition;
use Kajona\Flow\System\FlowConditionAbstract;
use Kajona\Flow\System\FlowConditionResult;
use Kajona\Flow\System\FlowTransition;
use Kajona\Flow\Tests\FlowTestAbstract;
use Kajona\System\System\MessagingMessage;

class GroupConditionTest extends FlowTestAbstract
{
    /**
     * @dataProvider evaluationDataProvider
     */
    public function testValidateCondition($left, $right, $expects)
    {
        $leftCondition = new CallbackCondition(function() use ($left){
            return new FlowConditionResult($left);
        });
        $rightCondition = new CallbackCondition(function() use ($right){
            return new FlowConditionResult($right);
        });

        $condition = $this->newCondition([$leftCondition, $rightCondition]);
        $result = $condition->validateCondition(new MessagingMessage(), new FlowTransition());

        $this->assertEquals($expects, $result->isValid());
    }

    public function evaluationDataProvider()
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false],
        ];
    }

    /**
     * @param array $childConditions
     * @return FlowConditionAbstract
     */
    protected function newCondition(array $childConditions)
    {
        $condition = $this->getMockBuilder(GroupCondition::class)
            ->disableOriginalConstructor()
            ->setMethods(["getChildConditions"])
            ->getMock();

        $condition->expects($this->once())
            ->method("getChildConditions")
            ->willReturn($childConditions);

        return $condition;
    }
}
