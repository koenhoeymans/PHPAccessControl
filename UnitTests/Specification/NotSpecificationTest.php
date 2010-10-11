<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

class PHPAccessControl_Specification_NotSpecificationTest extends PHPUnit_Framework_TestCase
{
	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isSatisfiedIfContainingIsNotSatisfied()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notGt2 = new \PHPAccessControl\Specification\NotSpecification($gt2);
		$this->assertTrue($notGt2->isSatisfiedBy(1));
		$this->assertFalse($notGt2->isSatisfiedBy(3));
	}

	// ----- is equal to -----

	// ----- is special case of -----

	/**
	 * @test
	 */
	public function isSpecialCaseIfContainingIsGeneralization()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$lt3 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(3);
		$notGt2 = new \PHPAccessControl\Specification\NotSpecification($gt2);
		$this->assertTrue($notGt2->isSpecialCaseOf($lt3));
	}

	/**
	 * @test
	 */
	public function isGeneralizationIfContainingIsSpecialCase()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$lt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(1);
		$notLt1 = new \PHPAccessControl\Specification\NotSpecification($lt1);
		$this->assertTrue($notLt1->isGeneralizationOf($gt2));
	}

	// ----- not -----

	/**
	 * @test
	 */
	public function notNotSpecificationIsEqualToSpecification()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notGt2 = new \PHPAccessControl\Specification\NotSpecification($gt2);
		$this->assertEquals($notGt2->not(), $gt2);
	}
}