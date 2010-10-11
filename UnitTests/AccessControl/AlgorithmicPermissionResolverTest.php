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
			$this->permissionResolver->isAllowedByInheritance(UserViewPost::create())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingAllowingRuleExists()
	{
		$this->rules->addRule(CreateRule::allow(UserViewPost::create()));
		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(UserViewPost::create())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingDenyingRuleExists()
	{
		$this->rules->addRule(CreateRule::deny(UserViewPost::create()));
		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(UserViewPost::create())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenNotAllowedNorDeniedButMoreGeneralSpecificationisAllowedByInheritance()
	{
		$this->rules->addRule(CreateRule::allow(UserViewPost::create()));
		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(UserViewPost::withCategoryIdEquals5())
		);
	}

	/**
	 * @test
	 */
	public function withMultipleLevelsOfAccessRightsTheClosestOneDeterminesInheritedPermission()
	{
		$this->rules->addRule(CreateRule::allow(UserViewPost::create()));
		$this->rules->addRule(CreateRule::deny(UserViewPost::withCategoryIdEquals5()));
		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(
				UserViewPost::withPostCategoryIdEquals5AndWordCountGreaterThan100()
			)
		);
	}

	/**
	 * @test
	 */
	public function allowedWinsFromDenied()
	{
		$this->rules->addRule(CreateRule::deny(UserViewPost::create()));
		$this->rules->addRule(CreateRule::allow(UserViewPost::create()));
		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(UserViewPost::create())
		);
	}
}