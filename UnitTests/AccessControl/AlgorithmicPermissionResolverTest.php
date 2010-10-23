<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Aco;
use PHPAccessControl\UnitTests\Support\CreateRule;
use PHPAccessControl\UnitTests\Support\Situation;
use PHPAccessControl\Property\PropertyDSL as Property;

/**
 * @todo http://groups.google.com/group/growing-object-oriented-software/msg/c2acdb54f75a6cba
 * don't use mocks here but fake implementation?
 * 
 */
class PHPAccessControl_AlgorithmicPermissionResolverTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->situationStore = $this->getMock('PHPAccessControl\\Situation\\SituationStore');

		$this->ruleFinder = $this->getMock('PHPAccessControl\\Rule\\RuleFinder');

		$this->permissionResolver = new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver(
			$this->ruleFinder,
			$this->situationStore
		);
	}

	// -- permissions --

	/**
	 * @test
	 */
	public function specificationIsDeniedWhenThereIsNoMatchingRule()
	{
		$this->ruleFinder
				->expects($this->once())
				->method('findMostSpecificMatchingRulesFor')
				->will($this->returnValue(array()));

		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingAllowingRuleExists()
	{
		$this->ruleFinder
			->expects($this->once())
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow(Situation::userViewPost()))));

		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenMatchingDenyingRuleExists()
	{
		$this->ruleFinder
			->expects($this->once())
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny(Situation::userViewPost()))));

		$this->assertFalse(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	/**
	 * @test
	 */
	public function isAllowedByInheritanceWhenNotAllowedNorDeniedButMoreGeneralSpecificationisAllowedByInheritance()
	{
		$this->ruleFinder
			->expects($this->once())
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow(Situation::userViewPost()))));

		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPostWithCategoryIdEquals5())
		);
	}

	/**
	 * @test
	 */
	public function withMultipleLevelsOfAccessRightsTheClosestOneDeterminesInheritedPermission()
	{
		$rules = array(
			CreateRule::deny(Situation::userViewPostWithCategoryIdEquals5())
		);
		$this->ruleFinder
			->expects($this->once())
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue($rules));

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
		$rules = array(
			CreateRule::deny(Situation::userViewPost()),
			CreateRule::allow(Situation::userViewPost())
		);
		$this->ruleFinder
			->expects($this->once())
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue($rules));

		$this->assertTrue(
			$this->permissionResolver->isAllowedByInheritance(Situation::userViewPost())
		);
	}

	// -- conditions --

	/**
	 * @test
	 */
	public function noAccessConditionsWhenRulesDontContainAcosThatAreFurtherSpecified()
	{
		$this->ruleFinder
			->expects($this->once())
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array()));

		$this->situationStore
			->expects($this->once())
			->method('getChildren')
			->will($this->returnValue(array()));

		$this->assertNull($this->permissionResolver->buildAccessConditionsFor(Situation::userViewPost()));
	}

	/**
	 * @test
	 */
	public function accessIsConditionalForSituationWithoutRuleWhenFurtherSpecifiedSituationExistsInAllowingRule()
	{
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array()));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		$this->assertEquals(
			Aco::named('post')->with(Property::named('wordcount')->greaterThan(100)), $result
		);
	}

	/**
	 * @test
	 */
	public function accessIsNotConditionalForAllowedSituationAndAllowedSpecifyingSituation()
	{
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($situation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function accessIsNotConditionalForDeniedSituationAndDeniedSpecifyingSituation()
	{
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($situation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function accessIsConditionalForSituationAllowedButChildSituationDenied()
	{
		$situation = Situation::userViewPost();
		$childSituation = Situation::userViewPostWithWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($situation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($situation);

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($parentSituation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($situation))));
		$this->ruleFinder
			->expects($this->at(2))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($situation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(2))
			->method('getChildren')
			->will($this->returnValue(array()));


		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		/**
		 * @todo
		 * -make this readable
		 * -make this less brittle (eg order of Or shouldn't be important)
		 */
		$this->assertEquals(
			Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))->not()
				->lOr(Aco::named('post')->with(
					Property::named('categoryId')->equals(5)
						->lAnd(Property::named('wordcount')->greaterThan(100)))),
			$result
		);
	}

	/**
	 * @test
	 */
	public function conditionalAccessForDeniedAllowedDenied()
	{
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($parentSituation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($situation))));
		$this->ruleFinder
			->expects($this->at(2))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($situation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(2))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($parentSituation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($situation))));
		$this->ruleFinder
			->expects($this->at(2))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($situation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(2))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

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
		$parentSituation = Situation::userViewPost();
		$situation = Situation::userViewPostWithWordCountGreaterThan100();
		$childSituation = Situation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($parentSituation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($situation))));
		$this->ruleFinder
			->expects($this->at(2))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($childSituation))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($situation)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array($childSituation)));
		$this->situationStore
			->expects($this->at(2))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

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
		$parentSituation = Situation::userViewPost();
		$situationA = Situation::userViewPostWithCategoryIdEquals5();
		$situationB = Situation::userViewPostWithWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($parentSituation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($situationA))));
		$this->ruleFinder
			->expects($this->at(2))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($situationB))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($situationA, $situationB)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array()));
		$this->situationStore
			->expects($this->at(2))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

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
		$parentSituation = Situation::userViewPost();
		$situationA = Situation::userViewPostWithCategoryIdEquals5();
		$situationB = Situation::userViewPostWithWordCountGreaterThan100();

		$this->ruleFinder
			->expects($this->at(0))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::allow($parentSituation))));
		$this->ruleFinder
			->expects($this->at(1))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($situationA))));
		$this->ruleFinder
			->expects($this->at(2))
			->method('findMostSpecificMatchingRulesFor')
			->will($this->returnValue(array(CreateRule::deny($situationB))));

		$this->situationStore
			->expects($this->at(0))
			->method('getChildren')
			->will($this->returnValue(array($situationA, $situationB)));
		$this->situationStore
			->expects($this->at(1))
			->method('getChildren')
			->will($this->returnValue(array()));
		$this->situationStore
			->expects($this->at(2))
			->method('getChildren')
			->will($this->returnValue(array()));

		$result = $this->permissionResolver->buildAccessConditionsFor($parentSituation);

		$this->assertEquals(
			Aco::named('post')->with(Property::named('categoryId')->equals(5))->not()
				->lAnd(Aco::named('post')->with(Property::named('wordcount')->greaterThan(100))->not()),
			$result
		);
	}
}