<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation;
use PHPAccessControl\UnitTests\Support\CreateRule;

class PHPAccessControl_AlgorithmicPermissionResolverTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->rules = new \PHPAccessControl\Rule\InMemoryRuleList();
		$this->situationStore = new \PHPAccessControl\Situation\InMemorySituationStore();
		$this->ruleFinder = new \PHPAccessControl\Rule\SimpleRuleFinder($this->rules, $this->situationStore);
		$this->permissionResolver =
			new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver($this->ruleFinder);
	}

	/**
	 * @test
	 */
	public function specificationIsDeniedWhenThereIsNoMatchingRule()
	{
		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingAllowingRuleExists()
	{
		$this->rules->addRule(CreateRule::allow(Situation::userViewPost()));
		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingDenyingRuleExists()
	{
		$this->rules->addRule(CreateRule::deny(Situation::userViewPost()));
		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenNotAllowedNorDeniedButMoreGeneralSpecificationisAllowedByInheritance()
	{
		$this->rules->addRule(CreateRule::allow(Situation::userViewPost()));
		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPostWithCategoryIdEquals5())
		);
	}

	/**
	 * @test
	 */
	public function withMultipleLevelsOfAccessRightsTheClosestOneDeterminesInheritedPermission()
	{
		$this->rules->addRule(CreateRule::allow(Situation::userViewPost()));
		$this->rules->addRule(CreateRule::deny(Situation::userViewPostWithCategoryIdEquals5()));
		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(
				Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100()
			)
		);
	}

	/**
	 * @test
	 */
	public function allowedWinsFromDenied()
	{
		$this->rules->addRule(CreateRule::deny(Situation::userViewPost()));
		$this->rules->addRule(CreateRule::allow(Situation::userViewPost()));
		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}
}