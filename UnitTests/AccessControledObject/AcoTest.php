<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\AccessControledObject\Aco;
use PHPAccessControl\Property\PropertyDSL as aProperty;

class PHPAccessControl_AccessControledObject_AcoTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function staticConstructorProvidesHigherLevelDSL()
	{
		$postWithNew = Aco::named('post');
		$postWithStatic = Aco::named('post');
		$this->assertEquals($postWithNew, $postWithStatic);
	}

	/**
	 * @test
	 */
	public function staticConstructorForTheAnyAcoProvidesHigherLevelDSL()
	{
		$anyAcoInstantiadedWithNew = new Aco();
		$anyAcoWithStatic = Aco::any();
		$this->assertEquals($anyAcoInstantiadedWithNew, $anyAcoWithStatic);
	}

	//------------------- is special case of / generalization of -------------------//

	/**
	 * @test
	 */
	public function anAcoIsSpecialCaseOfTheAnyAco()
	{
		$post = new Aco('post');
		$anyAco = new Aco();
		$this->assertTrue($post->isSpecialCaseOf($anyAco));
		$this->assertFalse($anyAco->isSpecialCaseOf($post));
	}

	/**
	 * @test
	 */
	public function theAnyAcoIsMoreGeneralThanAco()
	{
		$post = new Aco('post');
		$anyAco = new Aco();
		$this->assertTrue($anyAco->isGeneralizationOf($post));
		$this->assertFalse($post->isGeneralizationOf($anyAco));		
	}

	/**
	 * @test
	 */
	public function acoWithDifferentNameIsNotSpecialCase()
	{
		$post = new Aco('post');
		$category = new Aco('category');
		$this->assertFalse($post->isSpecialCaseOf($category));
	}

	/**
	 * @test
	 */
	public function twoIdenticalAcosAreSpecialCasesOfEachOther()
	{
		$aco1 = Aco::named('post')->with(aProperty::named('category'));
		$aco2 = Aco::named('post')->with(aProperty::named('category'));
		$this->assertTrue($aco1->isSpecialCaseOf($aco2));
	}

	/**
	 * @test
	 */
	public function twoIdenticalAcosAreGeneralizationsOfEachOther()
	{
		$aco1 = Aco::named('post')->with(aProperty::named('category'));
		$aco2 = Aco::named('post')->with(aProperty::named('category'));
		$this->assertTrue($aco1->isGeneralizationOf($aco2));
	}

	/**
	 * @test
	 */
	public function anAcoIsMoreSpecificWhenSameNamePlusSpecification()
	{
		$aco1 = Aco::named('post');
		$aco2 = Aco::named('post')->with(aProperty::named('category')->equals(5));
		$this->assertFalse($aco1->isSpecialCaseOf($aco2));
		$this->assertTrue($aco2->isSpecialCaseOf($aco1));
	}

	/**
	 * @test
	 */
	public function anAcoIsMoreSpecificWhenSameNameAndSpecificationPlusExtraSpecification()
	{
		$aco1 = Aco::named('post')->with(aProperty::named('category'));
		$aco2 = Aco::named('post')->with(aProperty::named('category')->lAnd(aProperty::named('wordcount'))); 
		$this->assertFalse($aco1->isSpecialCaseOf($aco2));
		$this->assertTrue($aco2->isSpecialCaseOf($aco1));
	}

	/**
	 * @test
	 */
	public function anAcoWithPropertyIsMoreSpecificThanAcoWithSameButMoreSpecificProperty()
	{
		$postWithWordcountGt150 = Aco::named('post')->with(aProperty::named('wordcount')->greaterThan(150));
		$postWithWordcountGt100 = Aco::named('post')->with(aProperty::named('wordcount')->greaterThan(100));
		$this->assertTrue($postWithWordcountGt150->isSpecialCaseOf($postWithWordcountGt100));
	}

	/**
	 * @test
	 */
	public function anAcoIsASpecialCaseOfACompositeOrAco()
	{
		$postWithWordCountGt100 = Aco::named('post')->with(aProperty::named('wordcount')->greaterThan(100));
		$postWithWordCountGt100AndCatId5 = Aco::named('post')->with(
			aProperty::named('wordcount')->greaterThan(100)->lOr(aProperty::named('categoryId')->equals(5))
		);
		$this->assertTrue(
			$postWithWordCountGt100->isSpecialCaseOf($postWithWordCountGt100AndCatId5)
		);
	}
}