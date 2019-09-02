<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\GenericSpecificationImplementation as GenericSpecification;

/**
 * @todo replace when issue resolved:
 * http://github.com/sebastianbergmann/phpunit-mock-objects/issues/#issue/22
 */
class PHPAccessControl_Specification_GenericSpecificationTest extends PHPUnit_Framework_TestCase
{
	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isNeverSatisfied()
	{
		$specification = new GenericSpecification();
		$this->assertFalse($specification->isSatisfiedBy(1));
	}

	// ----- equal -----

	// ----- Special Case Of, Generalization Of -----

	/**
	 * @test
	 */
	public function seeksMethodIsSpecialCaseOfOtherClassNameWhenComparingSpecialCaseWithOtherSpecification()
	{
		$specification1 = $specification1 = $this->getMock(
			'PHPAccessControl\\UnitTests\\Support\\GenericSpecificationImplementation',
			array('isSpecialCaseOfGenericSpecification')
		);
		$specification2 = new GenericSpecification();
		$specification1
			->expects($this->any())
			->method('isSpecialCaseOfGenericSpecification')
			->will($this->returnValue(true));
		$this->assertTrue($specification1->isSpecialCaseOf($specification2));
	}

	/**
	 * @test
	 */
	public function seeksMethodIsGeneralizationOfOtherClassNameWhenComparingSpecialCaseWithOtherSpecification()
	{
		$specification1 = $specification1 = $this->getMock(
			'PHPAccessControl\\UnitTests\\Support\\GenericSpecificationImplementation',
			array('isGeneralizationOfGenericSpecification')
		);
		$specification2 = new GenericSpecification();
		$specification1
			->expects($this->any())
			->method('isGeneralizationOfGenericSpecification')
			->will($this->returnValue(true));
		$this->assertTrue($specification1->isGeneralizationOf($specification2));
	}

	/**
	 * @test
	 */
	public function isNotSpecialCaseOfAnySpecification()
	{
		$specification1 = new GenericSpecification();
		$specification2 = new GenericSpecification();
		$this->assertFalse($specification1->isSpecialCaseOf($specification2));
	}

	/**
	 * @test
	 */
	public function isNotGeneralizationOfAnySpecification()
	{
		$specification1 = new GenericSpecification();
		$specification2 = new GenericSpecification();
		$this->assertFalse($specification1->isGeneralizationOf($specification2));
	}
}