<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use \PHPAccessControl\UnitTests\Support\CreateRule;
use \PHPAccessControl\UnitTests\Support\Situation;

class PHPAccessControl_Rule_SimpleRuleFinderTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->situationStore = new \PHPAccessControl\Situation\InMemorySituationStore();
		$this->ruleList = new \PHPAccessControl\Rule\InMemoryRuleList();
		$this->ruleFinder = new \PHPAccessControl\Rule\SimpleRuleFinder(
			$this->ruleList, $this->situationStore
		);
	}

	/**
	 * @test
	 */
	public function mostSpecificMatchingRulesAreTheOnesWithSameSituation()
	{
		// given
		$rule = CreateRule::allow(Situation::UserViewPost());
		$this->ruleList->addRule($rule);

		// when
		$mostSpecificMatchingRules = $this->ruleFinder->findMostSpecificMatchingRulesFor(
			Situation::UserViewPost()
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
		$rule = CreateRule::allow(Situation::UserViewPost());
		$this->ruleList->addRule($rule);

		// when
		$mostSpecificMatchingRules = $this->ruleFinder->findMostSpecificMatchingRulesFor(
			Situation::UserViewPostWithCategoryIdEquals5()
		);

		// then
		$this->assertEquals(array($rule), $mostSpecificMatchingRules);
	}
}