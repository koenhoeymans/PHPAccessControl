<?php

namespace PHPAccessControl\Rule;

use PHPAccessControl\CreateRule;
use PHPAccessControl\TestSituation;

class SimpleRuleFinderTest extends \PHPUnit\Framework\TestCase
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
		$rule = CreateRule::allow(TestSituation::UserViewPost());
		$this->ruleList->addRule($rule);

		// when
		$mostSpecificMatchingRules = $this->ruleFinder->findMostSpecificMatchingRulesFor(
			TestSituation::UserViewPost()
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
		$rule = CreateRule::allow(TestSituation::UserViewPost());
		$this->ruleList->addRule($rule);

		// when
		$mostSpecificMatchingRules = $this->ruleFinder->findMostSpecificMatchingRulesFor(
			TestSituation::UserViewPostWithCategoryIdEquals5()
		);

		// then
		$this->assertEquals(array($rule), $mostSpecificMatchingRules);
	}
}
