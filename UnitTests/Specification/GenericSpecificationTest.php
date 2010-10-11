<?php

use PHPAccessControl\Specification\GenericSpecification;
require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'SpecificationClasses.php';


class PHPAccessControl_Specification_GenericSpecificationTest extends PHPUnit_Framework_TestCase
{
	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isNeverSatisfied()
	{
		$specification = new GenericSpecificationTestClass();
		$this->assertFalse($specification->isSatisfiedBy(1));
	}

	// ----- equal -----

	// ----- Special Case Of, Generalization Of -----

	/**
	 * @test
	 */
	public function seeksMethodIsSpecialCaseOfOtherClassNameWhenComparingSpecialCaseWithOtherSpecification()
	{
		$specification1 = $this->getMock(
			'GenericSpecificationTestClass',
			array('isSpecialCaseOfOtherGenericSpecificationTestClass')
		);
		$specification2 = new OtherGenericSpecificationTestClass();
		$specification1
			->expects($this->any())
			->method('isSpecialCaseOfOtherGenericSpecificationTestClass')
			->will($this->returnValue(true));
		$this->assertTrue($specification1->isSpecialCaseOf($specification2));
	}

	/**
	 * @test
	 */
	public function seeksMethodIsGeneralizationOfOtherClassNameWhenComparingSpecialCaseWithOtherSpecification()
	{
		$specification1 = $this->getMock(
			'GenericSpecificationTestClass',
			array('isGeneralizationOfOtherGenericSpecificationTestClass')
		);
		$specification2 = new OtherGenericSpecificationTestClass();
		$specification1
			->expects($this->any())
			->method('isGeneralizationOfOtherGenericSpecificationTestClass')
			->will($this->returnValue(true));
		$this->assertTrue($specification1->isGeneralizationOf($specification2));
	}

	/**
	 * @test
	 */
	public function isNotSpecialCaseOfOtherClasses()
	{
		$specification1 = new GenericSpecificationTestClass();
		$specification2 = new OtherGenericSpecificationTestClass();
		$this->assertFalse($specification1->isSpecialCaseOf($specification2));
	}

	/**
	 * @test
	 */
	public function seeksMethodIsGeneralizationOfOtherClassNameWhenComparingGeneralizationWithOtherSpecification()
	{
		$specification1 = $this->getMock(
			'GenericSpecificationTestClass',
			array('isGeneralizationOfOtherGenericSpecificationTestClass')
		);
		$specification2 = new OtherGenericSpecificationTestClass();
		$specification1
			->expects($this->any())
			->method('isGeneralizationOfOtherGenericSpecificationTestClass')
			->will($this->returnValue(true));
		$this->assertTrue($specification1->isGeneralizationOf($specification2));
	}

	/**
	 * @test
	 */
	public function isNotGeneralizationOfOtherClasses()
	{
		$specification1 = new GenericSpecificationTestClass();
		$specification2 = new OtherGenericSpecificationTestClass();
		$this->assertFalse($specification1->isGeneralizationOf($specification2));
	}
}