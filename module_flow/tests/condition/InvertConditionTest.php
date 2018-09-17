<?php

namespace Kajona\Flow\Tests\Condition;

use Kajona\Flow\System\Flow\Condition\CallbackCondition;
use Kajona\Flow\System\Flow\Condition\XorCondition;
use Kajona\Flow\System\FlowConditionAbstract;
use Kajona\Flow\System\FlowConditionResult;
use Kajona\Flow\System\FlowTransition;
use Kajona\Flow\Tests\FlowTestAbstract;
use Kajona\System\System\MessagingMessage;

class InvertConditionTest extends FlowTestAbstract
{
    public function testValidateCondition()
    {
        $invertCondition = new CallbackCondition(function(){
            return new FlowConditionResult(false);
        });

        $condition = $this->newCondition([$invertCondition]);
        $result = $condition->validateCondition(new MessagingMessage(), new FlowTransition());

        $this->assertEquals(true, $result->isValid());
    }

    /**
     * @param array $childConditions
     * @return FlowConditionAbstract
     */
    protected function newCondition(array $childConditions)
    {
        $condition = $this->getMockBuilder(XorCondition::class)
            ->disableOriginalConstructor()
            ->setMethods(["getChildConditions"])
            ->getMock();

        $condition->expects($this->once())
            ->method("getChildConditions")
            ->willReturn($childConditions);

        return $condition;
    }
}
