<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\CreateRule;
use \PHPAccessControl\UnitTests\Support\Situation;

class PHPAccessControl_Rule_InMemoryRuleListTest extends PHPUnit_Framework_TestCase
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
		
		$mockObserver = $this->getMock('PHPAccessControl\\Rule\\RuleListObserver');
		$mockObserver
			->expects($this->once())
			->method('notifyRuleAdded')
			->with($this->equalTo(CreateRule::allow(Situation::UserViewPost()))
			);
		$this->ruleList->addObserver($mockObserver);
		$this->ruleList->addRule(CreateRule::allow(Situation::UserViewPost()));
	}
}