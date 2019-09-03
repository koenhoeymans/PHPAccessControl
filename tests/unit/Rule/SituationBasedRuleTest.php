<?php

namespace PHPAccessControl\Rule;

use PHPAccessControl\TestSituation;
use PHPAccessControl\Rule\SituationBasedRule;

class SituationBasedRuleTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @test
	 */
	public function tellsSpecificationIsAllowed()
	{
		$rule = new SituationBasedRule(TestSituation::UserViewPost(), true);
		$this->assertTrue($rule->isAllowed());
	}

	/**
	 * @test
	 */
	public function tellsSpecificationIsDenied()
	{
		$rule = new SituationBasedRule(TestSituation::UserViewPost(), false);
		$this->assertFalse($rule->isAllowed());
	}

	/**
	 * @test
	 */
	public function canDetermineIfItAppliesToASpecification()
	{
		$rule = new SituationBasedRule(TestSituation::UserViewPost(), true);
		$this->assertTrue($rule->appliesTo(TestSituation::UserViewPostWithCategoryIdEquals5()));
		$this->assertTrue($rule->appliesTo(TestSituation::UserViewPostWithWordCountGreaterThan100()));
	}
}
