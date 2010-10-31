<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation;

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
		$this->assertTrue($this->arraysHaveEqualElements(
			array(Situation::userViewPostWithWordCountGreaterThan100()),
			$this->permissionList->findChildren(Situation::userViewPost())
		));
	}

	/**
	 * @test
	 */
	public function whenPermissionWasAssignedToAParentSituationItIsFoundAsParent()
	{
		$this->permissionList->deny(Situation::userViewPost());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(Situation::userViewPost()),
			$this->permissionList->findParents(Situation::userViewPostWithWordCountGreaterThan100())
		));
	}

	/**
	 * @test
	 */
	public function onlyTheClosestParentIsReturned()
	{
		$this->permissionList->deny(Situation::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->allow(Situation::userViewPost());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(
				Situation::userViewPostWithWordCountGreaterThan100(),
			),
			$this->permissionList->findParents(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		));
	}

	/**
	 * @test
	 */
	public function withMultipleSituationsAllClosestParentsAreReturned()
	{
		$this->permissionList->allow(Situation::userViewPost());
		$this->permissionList->deny(Situation::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->deny(Situation::userViewPostWithCategoryIdEquals5());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(
				Situation::userViewPostWithWordCountGreaterThan100(),
				Situation::userViewPostWithCategoryIdEquals5()
			),
			$this->permissionList->findParents(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		));
	}

	/**
	 * @test
	 */
	public function onlyTheClosestChildIsReturned()
	{
		$this->permissionList->deny(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100());
		$this->permissionList->allow(Situation::userViewPostWithWordCountGreaterThan100());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(Situation::userViewPostWithWordCountGreaterThan100()),
			$this->permissionList->findChildren(Situation::userViewPost())
		));
	}

	/**
	 * @test
	 */
	public function withMultipleSituationsAllClosestChildrenAreReturned()
	{
		$this->permissionList->deny(Situation::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->deny(Situation::userViewPostWithCategoryIdEquals5());
		$this->permissionList->deny(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100());
		$this->assertTrue($this->arraysHaveEqualElements(
			array(
				Situation::userViewPostWithWordCountGreaterThan100(),
				Situation::userViewPostWithCategoryIdEquals5()
			),
			$this->permissionList->findChildren(Situation::userViewPost())
		));
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedIsNotReturnedAsParentItself()
	{
		$this->permissionList->allow(Situation::userViewPost());
		$this->assertEquals(
			array(),
			$this->permissionList->findParents(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedIsNotReturnedAsChildItself()
	{
		$this->permissionList->allow(Situation::userViewPost());
		$this->assertEquals(
			array(),
			$this->permissionList->findChildren(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedThatIsNotGeneralizationIsNotReturnedAsParent()
	{
		$this->permissionList->allow(Situation::userViewPostWithWordCountGreaterThan100());
		$this->assertEquals(
			array(),
			$this->permissionList->findParents(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function situationWithPermissionAssignedThatIsNotSpecialCaseIsNotReturnedAsParent()
	{
		$this->permissionList->allow(Situation::userViewPost());
		$this->assertEquals(
			array(),
			$this->permissionList->findChildren(Situation::userViewPostWithWordCountGreaterThan100())
		);
	}
}