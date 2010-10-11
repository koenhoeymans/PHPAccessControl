<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'SituationClasses.php';
require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'RuleClasses.php';

class PHPAccessControl_Rule_SimpleRuleFinderTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->situationStore = new \PHPAccessControl\Situation\InMemorySituationStore();
		$this->ruleList = new \PHPAccessControl\Rule\InMemoryRuleList();
		$this->ruleFinder = new \PHPAccessControl\Rule\SimpleRuleFinder($this->ruleList, $this->situationStore);
	}

	/**
	 * @test
	 */
	public function mostSpecificMatchingRulesAreTheOnesWithSameSituation()
	{
		// given
		$rule = CreateRule::allow(UserViewPost::create());
		$this->ruleList->addRule(CreateRule::allow(UserViewPost::create()));

		// when
		$mostSpecificMatchingRules = $this->ruleFinder->findMostSpecificMatchingRulesFor(
			UserViewPost::create()
		);

		// then
		$this->assertEquals(array($rule), $mostSpecificMatchingRules);
	}

	/**
	 * @test
	 */
	public function mostSpecificMatchingRulesAreTheOnesWithMoreGeneralSituationIfNoneWithSameSituation()
	{
		// given
		$rule = CreateRule::allow(UserViewPost::create());
		$this->ruleList->addRule($rule);

		// when
		$mostSpecificMatchingRules = $this->ruleFinder->findMostSpecificMatchingRulesFor(
			UserViewPost::withCategoryIdEquals5()
		);

		// then
		$this->assertEquals(array($rule), $mostSpecificMatchingRules);
	}
}