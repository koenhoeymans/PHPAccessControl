<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation;
use PHPAccessControl\Rule\SituationBasedRule;

class PHPAccessControl_Rule_SituationBasedRuleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function tellsSpecificationIsAllowed()
	{
		$rule = new SituationBasedRule(Situation::UserViewPost(), true);
		$this->assertTrue($rule->isAllowed());
	}

	/**
	 * @test
	 */
	public function tellsSpecificationIsDenied()
	{
		$rule = new SituationBasedRule(Situation::UserViewPost(), false);
		$this->assertFalse($rule->isAllowed());
	}

	/**
	 * @test
	 */
	public function canDetermineIfItAppliesToASpecification()
	{
		$rule = new SituationBasedRule(Situation::UserViewPost(), true);
		$this->assertTrue($rule->appliesTo(Situation::UserViewPostWithCategoryIdEquals5()));
		$this->assertTrue($rule->appliesTo(Situation::UserViewPostWithWordCountGreaterThan100()));
	}
}