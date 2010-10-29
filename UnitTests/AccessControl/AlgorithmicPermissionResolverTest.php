<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\AccessControledObject\Aco;
use PHPAccessControl\UnitTests\Support\Situation;
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
			$this->permissionResolver->isAllowed(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingAllowingRuleExists()
	{
		$this->permissionList->allow(Situation::userViewPost());

		$this->assertTrue(
			$this->permissionResolver->isAllowed(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingDenyingRuleExists()
	{
		$this->permissionList->deny(Situation::userViewPost());

		$this->assertFalse(
			$this->permissionResolver->isAllowed(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenNotAllowedNorDeniedButMoreGeneralSpecificationisAllowedByInheritance()
	{
		$this->permissionList->allow(Situation::userViewPost());
		$this->permissionList->addParent(
			Situation::userViewPostWithCategoryIdEquals5(), Situation::userViewPost()
		);

		$this->assertTrue(
			$this->permissionResolver->isAllowed(Situation::userViewPostWithCategoryIdEquals5())
		);
	}

	/**
	 * @test
	 */
	public function withMultipleLevelsOfAccessRightsTheClosestOneDeterminesInheritedPermission()
	{
		$this->permissionList->allow(Situation::userViewPostWithCategoryIdEquals5());
		$this->permissionList->deny(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100());
		$this->permissionList->addParent(
			Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100(),
			Situation::userViewPostWithCategoryIdEquals5()
		);

		$this->assertFalse(
			$this->permissionResolver->isAllowed(
				Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100()
			)
		);
	}

	/**
	 * @test
	 */
	public function allowedWinsFromDeniedWhenCompetingPermissionsForParentSituations()
	{
		$this->permissionList->allow(Situation::userViewPostWithCategoryIdEquals5());
		$this->permissionList->deny(Situation::userViewPostWithWordCountGreaterThan100());
		$this->permissionList->addParent(
			Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100(),
			Situation::userViewPostWithCategoryIdEquals5()
		);
		$this->permissionList->addParent(
			Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100(),
			Situation::userViewPostWithWordCountGreaterThan100()
		);

		$this->assertTrue(
			$this->permissionResolver->isAllowed(Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100())
		);
	}

	// -- conditions --

	/**
	 * @test
	 */
	public function noAccessConditionsWhenRulesDontContainAcosThatAreFurtherSpecified()
	{
		$this->permissionList->allow(Situation::userViewPost());

		$this->assertNull($this->permissionResolver->buildAccessConditionsFor(Situation::userViewPost()));
	}

	/**
	 * @test
	 */
	public function accessIsConditionalForSituationWithoutRuleWhenFurtherSpecifiedSituationIsAllowed()
	{
		// given
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

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
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

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
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

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
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

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
		$parentSituation = Situation::userViewPost();
		$situationA = Situation::userViewPostWithCategoryIdEquals5();
		$situationB = Situation::userViewPostWithWordCountGreaterThan100();

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
		$parentSituation = Situation::userViewPost();
		$situationA = Situation::userViewPostWithCategoryIdEquals5();
		$situationB = Situation::userViewPostWithWordCountGreaterThan100();

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