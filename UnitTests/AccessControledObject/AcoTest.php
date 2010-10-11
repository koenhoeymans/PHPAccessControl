<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'AcoClasses.php';

class PHPAccessControl_AccessControledObject_AcoTest extends PHPUnit_Framework_TestCase
{
	//------------------- is special case of / generalization of -------------------//

	/**
	 * @test
	 */
	public function anAcoIsSpecialCaseOfTheAnyAco()
	{
		$post = CreateAco::name('post');
		$anyAco = new \PHPAccessControl\AccessControledObject\Aco();
		$this->assertTrue($post->isSpecialCaseOf($anyAco));
		$this->assertFalse($anyAco->isSpecialCaseOf($post));
	}

	/**
	 * @test
	 */
	public function theAnyAcoIsMoreGeneralThanAco()
	{
		$post = CreateAco::name('post');
		$anyAco = new \PHPAccessControl\AccessControledObject\Aco();
		$this->assertTrue($anyAco->isGeneralizationOf($post));
		$this->assertFalse($post->isGeneralizationOf($anyAco));		
	}

	/**
	 * @test
	 */
	public function acoWithDifferentNameIsNotSpecialCase()
	{
		$post = createAco::name('post');
		$category = createAco::name('category');
		$this->assertFalse($post->isSpecialCaseOf($category));
	}

	/**
	 * @test
	 */
	public function twoIdenticalAcosAreSpecialCasesOfEachOther()
	{
		$aco1 = createAco::postWithCategoryIdEquals5();
		$aco2 = createAco::postWithCategoryIdEquals5();
		$this->assertTrue($aco1->isSpecialCaseOf($aco2));
	}

	/**
	 * @test
	 */
	public function twoIdenticalAcosAreGeneralizationsOfEachOther()
	{
		$aco1 = createAco::postWithCategoryIdEquals5();
		$aco2 = createAco::postWithCategoryIdEquals5();
		$this->assertTrue($aco1->isGeneralizationOf($aco2));
	}

	/**
	 * @test
	 */
	public function anAcoIsMoreSpecificWhenSameNamePlusSpecification()
	{
		$aco1 = createAco::name('post');
		$aco2 = createAco::postWithCategoryIdEquals5(); 
		$this->assertFalse($aco1->isSpecialCaseOf($aco2));
		$this->assertTrue($aco2->isSpecialCaseOf($aco1));
	}

	/**
	 * @test
	 */
	public function anAcoIsMoreSpecificWhenSameNameAndSpecificationPlusExtraSpecification()
	{
		$aco1 = createAco::postWithCategoryIdEquals5();
		$aco2 = createAco::postWithCategoryIdEquals5AndWordCountGreaterThan100(); 
		$this->assertFalse($aco1->isSpecialCaseOf($aco2));
		$this->assertTrue($aco2->isSpecialCaseOf($aco1));
	}

	/**
	 * @test
	 */
	public function anAcoWithPropertyIsMoreSpecificThanAcoWithSameButMoreSpecificProperty()
	{
		$postWithWordcountGt100 = createAco::postWithWordCountGreaterThan(100);
		$postWithWordcountGt150 = createAco::postWithWordCountGreaterThan(150); 
		$this->assertTrue($postWithWordcountGt150->isSpecialCaseOf($postWithWordcountGt100));
	}

	/**
	 * @test
	 */
	public function anAcoIsASpecialCaseOfACompositeOrAco()
	{
		$this->assertTrue(
			createAco::postWithWordCountGreaterThan(100)->isSpecialCaseOf(
				createAco::postWithCategoryIdEquals5()->lOr(createAco::postWithWordCountGreaterThan(100))
			)
		);
	}
}