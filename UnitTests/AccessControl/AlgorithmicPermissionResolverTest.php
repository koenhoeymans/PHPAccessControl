<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\AccessControledObject\Aco;
use PHPAccessControl\UnitTests\Support\Situation as a;
use PHPAccessControl\Property\PropertyDSL as Property;

/**
 * http://groups.google.com/group/growing-object-oriented-software/msg/c2acdb54f75a6cba
 */
class PHPAccessControl_AlgorithmicPermissionResolverTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->permissionList = new \PHPAccessControl\UnitTests\Support\PermissionListMock();
		$this->permissionResolver = new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver(
			$this->permissionList
		);
	}

	// -- permissions --

	/**
	 * @test
	 */
	public function specificationIsDeniedWhenThereIsNoMatchingRule()
	{
		$this->assertFalse(
			$this->permissionResolver->isAllowed(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingAllowingRuleExists()
	{
		$this->permissionList->allow(a::userViewPost());

		$this->assertTrue(
			$this->permissionResolver->isAllowed(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingDenyingRuleExists()
	{
		$this->permissionList->deny(a::userViewPost());

		$this->assertFalse(
			$this->permissionResolver->isAllowed(a::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenNotAllowedNorDeniedButMoreGeneralSpecificationisAllowedByInheritance()
	{
		$this->permissionList->allow(a::userViewPost());
		$this->permissionList->addParent(
			a::userViewPostWithCategoryIdEquals5(), a::userViewPost()
		);

		$this->assertTrue(
			$this->permissionResolver->isAllowed(a::userViewPostWithCategoryIdEquals5())
		);
	}

	/**
	 * @test
	 */
	public function withMultipleLevelsOfAccessRightsTheClosestOneDeterminesInheritedPermission()
	{
		$this->permissionList->allow(a::userViewPostWithCategoryIdEquals5());
		$this->permissionList->deny(a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100());
		$this->permissionList->addParent(
			a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100(),
			a::userViewPostWithCategoryIdEquals5()
		);

		$this->assertFalse(
			$this->permissionResolver->isAllowed(
				a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100()
			)
		);
	}

	/**
	 * @test
	 */
	public function allowedWinsFromDeniedWhenCompetingPermissionsForParentSituations()
	{
		$this->permissionList->allow(a::userViewPostWithCategoryIdEquals5());
		$this->permissionList->deny(a::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->addParent(
			a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100(),
			a::userViewPostWithCategoryIdEquals5()
		);
		$this->permissionList->addParent(
			a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100(),
			a::userViewPostWithWordCountGreaterThan100()
		);

		$this->assertTrue(
			$this->permissionResolver->isAllowed(a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		);
	}

	// -- conditions --

	/**
	 * @test
	 */
	public function noAccessConditionsWhenRulesDontContainAcosThatAreFurtherSpecified()
	{
		$this->permissionList->allow(a::userViewPost());

		$this->assertNull($this->permissionResolver->buildAccessConditionsFor(a::userViewPost()));
	}

	/**
	 * @test
	 */
	public function accessIsConditionalForSituationWithoutRuleWhenFurtherSpecifiedSituationIsAllowed()
	{
		// given
		$situation = a::userViewPost();
		$childSituation = a::userViewPostWithWordCountGreaterThan100();

		$this->permissionList->allow($childSituation);
		$this->permissionList->addParent($childSituation, $situation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		// then
		$this->assertEquals(
			Aco::named('post')->with(Property::named('wordcount')->greaterThan(100)), $result
		);
	}

	/**
	 * @test
	 */
	public function accessIsNotConditionalForAllowedSituationAndAllowedSpecifyingSituation()
	{
		// given
		$situation = a::userViewPost();
		$childSituation = a::userViewPostWithWordCountGreaterThan100();

		$this->permissionList->allow($situation);
		$this->permissionList->allow($childSituation);
		$this->permissionList->addParent($childSituation, $situation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		// then
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function accessIsNotConditionalForDeniedSituationAndDeniedSpecifyingSituation()
	{
		// given
		$situation = a::userViewPost();
		$childSituation = a::userViewPostWithWordCountGreaterThan100();

		$this->permissionList->deny($situation);
		$this->permissionList->deny($childSituation);
		$this->permissionList->addParent($childSituation, $situation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		// then
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function accessIsConditionalForSituationAllowedButChildSituationDenied()
	{
		// given
		$situation = a::userViewPost();
		$childSituation = a::userViewPostWithWordCountGreaterThan100();

		$this->permissionList->allow($situation);
		$this->permissionList->deny($childSituation);
		$this->permissionList->addParent($childSituation, $situation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		// then
		$this->assertEquals(
			Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))->not(),
			$result
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForAllowedDeniedAllowed()
	{
		// given
		$parentSituation = a::userViewPost();
		$situation = a::userViewPostWithWordCountGreaterThan100();
		$childSituation = a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->permissionList->allow($parentSituation);
		$this->permissionList->deny($situation);
		$this->permissionList->allow($childSituation);
		$this->permissionList->addParent($childSituation, $situation);
		$this->permissionList->addParent($situation, $parentSituation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		// then
		$this->assertTrue(
			$result->isEqualTo(
				Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))->not()
					->lOr(
				Aco::named('post')->with(
							Property::named('categoryId')->equals(5)->lAnd(
							Property::named('wordcount')->greaterThan(100))
						)
					)
			)
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForDeniedAllowedDenied()
	{
		// given
		$parentSituation = a::userViewPost();
		$situation = a::userViewPostWithWordCountGreaterThan100();
		$childSituation = a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->permissionList->deny($parentSituation);
		$this->permissionList->allow($situation);
		$this->permissionList->deny($childSituation);
		$this->permissionList->addParent($childSituation, $situation);
		$this->permissionList->addParent($situation, $parentSituation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		// then
		$this->assertEquals(
			Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))
				->lAnd(
					Aco::named('post')
					->with(Property::named('categoryId')->equals(5)->lAnd(Property::named('wordcount')->greaterThan(100)))
					->not()
				),
			$result
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForAllowedAllowedDenied()
	{
		// given
		$parentSituation = a::userViewPost();
		$situation = a::userViewPostWithWordCountGreaterThan100();
		$childSituation = a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->permissionList->allow($parentSituation);
		$this->permissionList->allow($situation);
		$this->permissionList->deny($childSituation);
		$this->permissionList->addParent($childSituation, $situation);
		$this->permissionList->addParent($situation, $parentSituation);
		
		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		// then
		$this->assertEquals(
			Aco::named('post')
				->with(Property::named('categoryId')->equals(5)->lAnd(Property::named('wordcount')->greaterThan(100)))
				->not(),
			$result
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForDeniedDeniedAllowed()
	{
		// given
		$parentSituation = a::userViewPost();
		$situation = a::userViewPostWithWordCountGreaterThan100();
		$childSituation = a::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->permissionList->deny($parentSituation);
		$this->permissionList->deny($situation);
		$this->permissionList->allow($childSituation);
		$this->permissionList->addParent($childSituation, $situation);
		$this->permissionList->addParent($situation, $parentSituation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		// then
		$this->assertEquals(
			Aco::named('post')
				->with(Property::named('categoryId')->equals(5)->lAnd(Property::named('wordcount')->greaterThan(100))),
			$result
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForDeniedAndTwoChildSituationsAllowed()
	{
		// given
		$parentSituation = a::userViewPost();
		$situationA = a::userViewPostWithCategoryIdEquals5();
		$situationB = a::userViewPostWithWordCountGreaterThan100();

		$this->permissionList->deny($parentSituation);
		$this->permissionList->allow($situationA);
		$this->permissionList->allow($situationB);
		$this->permissionList->addParent($situationA, $parentSituation);
		$this->permissionList->addParent($situationB, $parentSituation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		// then
		$this->assertEquals(
			Aco::named('post')->with(Property::named('categoryId')->equals(5))
			->lOr(Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))),
			$result
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForAllowedAndTwoChildSituationsDenied()
	{
		// given
		$parentSituation = a::userViewPost();
		$situationA = a::userViewPostWithCategoryIdEquals5();
		$situationB = a::userViewPostWithWordCountGreaterThan100();

		$this->permissionList->allow($parentSituation);
		$this->permissionList->deny($situationA);
		$this->permissionList->deny($situationB);
		$this->permissionList->addParent($situationA, $parentSituation);
		$this->permissionList->addParent($situationB, $parentSituation);

		// when
		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		// then
		$this->assertEquals(
			Aco::named('post')->with(Property::named('categoryId')->equals(5))->not()
				->lAnd(Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))->not()),
			$result
		);
	}
}