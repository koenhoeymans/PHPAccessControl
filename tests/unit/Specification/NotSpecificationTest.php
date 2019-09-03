<?php

namespace PHPAccessControl\Specification;

use PHPAccessControl\Specification\NotSpecification;
use PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan;
use PHPAccessControl\Specification\ValueBoundSpecification\LesserThan;

class NotSpecificationTest extends \PHPUnit\Framework\TestCase
{
	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isSatisfiedIfContainingIsNotSatisfied()
	{
		$gt2 = new GreaterThan(2);
		$notGt2 = new NotSpecification($gt2);
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
		$gt2 = new GreaterThan(2);
		$lt3 = new LesserThan(3);
		$notGt2 = new NotSpecification($gt2);
		$this->assertTrue($notGt2->isSpecialCaseOf($lt3));
	}

	/**
	 * @test
	 */
	public function isGeneralizationIfContainingIsSpecialCase()
	{
		$gt2 = new GreaterThan(2);
		$lt1 = new LesserThan(1);
		$notLt1 = new NotSpecification($lt1);
		$this->assertTrue($notLt1->isGeneralizationOf($gt2));
	}

	// ----- not -----

	/**
	 * @test
	 */
	public function notNotSpecificationIsEqualToSpecification()
	{
		$gt2 = new GreaterThan(2);
		$notGt2 = new NotSpecification($gt2);
		$this->assertEquals($notGt2->not(), $gt2);
	}
}
