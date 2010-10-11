<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'SpecificationClasses.php';

class PHPAccessControl_Rule_SituationBasedRuleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function tellsSpecificationIsAllowed()
	{
		$rule = new PHPAccessControl\Rule\SituationBasedRule(UserViewPost::create(), true);
		$this->assertTrue($rule->isAllowed());
	}

	/**
	 * @test
	 */
	public function tellsSpecificationIsDenied()
	{
		$rule = new PHPAccessControl\Rule\SituationBasedRule(UserViewPost::create(), false);
		$this->assertFalse($rule->isAllowed());
	}

	/**
	 * @test
	 */
	public function canDetermineIfItAppliesToASpecification()
	{
		$rule = new PHPAccessControl\Rule\SituationBasedRule(UserViewPost::create(), true);
		$this->assertTrue($rule->appliesTo(UserViewPost::withCategoryIdEquals5()));
		$this->assertTrue($rule->appliesTo(UserViewPost::withWordCountGreaterThan100()));
	}
}