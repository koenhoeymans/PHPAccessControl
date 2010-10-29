<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation;

class PHPAccessControl_InMemoryRuleListTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->permissionList = new \PHPAccessControl\AccessControl\InMemoryPermissionList();
	}

	/**
	 * @test
	 */
	public function storesThatSituationIsAllowed()
	{
		$this->permissionList->allow(Situation::userViewPost());
		$this->assertTrue($this->permissionList->isAllowed(Situation::userViewPost()));
	}

	/**
	 * @test
	 */
	public function storesThatSituationIsDenied()
	{
		$this->permissionList->deny(Situation::userViewPost());
		$this->assertFalse($this->permissionList->isAllowed(Situation::userViewPost()));
	}

	/**
	 * @test
	 */
	public function whenSituationIsNeitherDeniedNorAllowedPermissionIsUnknown()
	{
		$this->assertNull($this->permissionList->isAllowed(Situation::userViewPost()));
	}

	/**
	 * @test
	 */
	public function whenNoRulesWereAddedThereAreNoParentSituations()
	{
		$this->assertEquals(
			array(),
			$this->permissionList->findParents(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function whenNoRulesWereAddedThereAreNoChildSituations()
	{
		$this->assertEquals(
			array(),
			$this->permissionList->findChildren(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function whenPermissionWasAssignedToAChildSituationItIsFoundAsChild()
	{
		$this->permissionList->deny(Situation::userViewPostWithWordCountGreaterThan100());
		$this->assertEquals(
			array(Situation::userViewPostWithWordCountGreaterThan100()),
			$this->permissionList->findChildren(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function whenPermissionWasAssignedToAParentSituationItIsFoundAsParent()
	{
		$this->permissionList->deny(Situation::userViewPost());
		$this->assertEquals(
			array(Situation::userViewPost()),
			$this->permissionList->findParents(Situation::userViewPostWithWordCountGreaterThan100())
		);
	}

	/**
	 * @test
	 */
	public function withMultipleSituationsOnlyClosestParentsAreReturned()
	{
		$this->permissionList->deny(Situation::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->allow(Situation::userViewPost());
		$this->assertEquals(
			array(Situation::userViewPostWithWordCountGreaterThan100()),
			$this->permissionList->findParents(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		);
	}
}