<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation as a;

class PHPAccessControl_InMemoryRuleListTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->customAssertions = new \PHPAccessControl\UnitTests\Support\CustomAssertions();
		$this->permissionList = new \PHPAccessControl\AccessControl\InMemoryPermissionList();
	}

	public function arraysHaveEqualElements($array1, $array2)
	{
		return $this->customAssertions->arraysHaveEqualElements($array1, $array2);
	}

	/**
	 * @test
	 */
	public function storesThatSituationIsAllowed()
	{
		$this->permissionList->allow(a::userViewPost());
		$this->assertTrue($this->permissionList->isAllowed(a::userViewPost()));
	}

	/**
	 * @test
	 */
	public function storesThatSituationIsDenied()
	{
		$this->permissionList->deny(a::userViewPost());
		$this->assertFalse($this->permissionList->isAllowed(a::userViewPost()));
	}

	/**
	 * @test
	 */
	public function whenSituationIsNeitherDeniedNorAllowedPermissionIsUnknown()
	{
		$this->assertNull($this->permissionList->isAllowed(a::userViewPost()));
	}

	/**
	 * @test
	 */
	public function whenNoRulesWereAddedThereAreNoParentSituations()
	{
		$this->assertEquals(
			array(),
			$this->permissionList->findParents(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function whenNoRulesWereAddedThereAreNoChildSituations()
	{
		$this->assertEquals(
			array(),
			$this->permissionList->findChildren(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function whenPermissionWasAssignedToAChildSituationItIsFoundAsChild()
	{
		$this->permissionList->deny(a::userViewPostWithWordCountGreaterThan100());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(a::userViewPostWithWordCountGreaterThan100()),
			$this->permissionList->findChildren(a::userViewPost())
		));
	}

	/**
	 * @test
	 */
	public function whenPermissionWasAssignedToAParentSituationItIsFoundAsParent()
	{
		$this->permissionList->deny(a::userViewPost());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(a::userViewPost()),
			$this->permissionList->findParents(a::userViewPostWithWordCountGreaterThan100())
		));
	}

	/**
	 * @test
	 */
	public function onlyTheClosestParentIsReturned()
	{
		$this->permissionList->deny(a::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->allow(a::userViewPost());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(
				a::userViewPostWithWordCountGreaterThan100(),
			),
			$this->permissionList->findParents(a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		));
	}

	/**
	 * @test
	 */
	public function withMultipleSituationsAllClosestParentsAreReturned()
	{
		$this->permissionList->allow(a::userViewPost());
		$this->permissionList->deny(a::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->deny(a::userViewPostWithCategoryIdEquals5());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(
				a::userViewPostWithWordCountGreaterThan100(),
				a::userViewPostWithCategoryIdEquals5()
			),
			$this->permissionList->findParents(a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		));
	}

	/**
	 * @test
	 */
	public function onlyTheClosestChildIsReturned()
	{
		$this->permissionList->deny(a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100());
		$this->permissionList->allow(a::userViewPostWithWordCountGreaterThan100());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(a::userViewPostWithWordCountGreaterThan100()),
			$this->permissionList->findChildren(a::userViewPost())
		));
	}

	/**
	 * @test
	 */
	public function withMultipleSituationsAllClosestChildrenAreReturned()
	{
		$this->permissionList->deny(a::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->deny(a::userViewPostWithCategoryIdEquals5());
		$this->permissionList->deny(a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(
				a::userViewPostWithWordCountGreaterThan100(),
				a::userViewPostWithCategoryIdEquals5()
			),
			$this->permissionList->findChildren(a::userViewPost())
		));
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedIsNotReturnedAsParentItself()
	{
		$this->permissionList->allow(a::userViewPost());
		$this->assertEquals(
			array(),
			$this->permissionList->findParents(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedIsNotReturnedAsChildItself()
	{
		$this->permissionList->allow(a::userViewPost());
		$this->assertEquals(
			array(),
			$this->permissionList->findChildren(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedThatIsNotGeneralizationIsNotReturnedAsParent()
	{
		$this->permissionList->allow(a::userViewPostWithWordCountGreaterThan100());
		$this->assertEquals(
			array(),
			$this->permissionList->findParents(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedThatIsNotSpecialCaseIsNotReturnedAsParent()
	{
		$this->permissionList->allow(a::userViewPost());
		$this->assertEquals(
			array(),
			$this->permissionList->findChildren(a::userViewPostWithWordCountGreaterThan100())
		);
	}
}