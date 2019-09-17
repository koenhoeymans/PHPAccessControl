<?php

namespace PHPAccessControl\Rule;

use PHPAccessControl\CreateRule;
use PHPAccessControl\TestSituation;

class InMemoryRuleListTest extends \PHPUnit\Framework\TestCase
{
    public function setup()
    {
        $this->ruleList = new \PHPAccessControl\Rule\InMemoryRuleList();
    }

    /**
     * @test
     */
    public function notifiesObserversOfNewlyAddedRules()
    {
        
        $mockObserver = $this->createMock('PHPAccessControl\\Rule\\RuleListObserver');
        $mockObserver
            ->expects($this->once())
            ->method('notifyRuleAdded')
            ->with($this->equalTo(CreateRule::allow(TestSituation::UserViewPost())));
        $this->ruleList->addObserver($mockObserver);
        $this->ruleList->addRule(CreateRule::allow(TestSituation::UserViewPost()));
    }
}
