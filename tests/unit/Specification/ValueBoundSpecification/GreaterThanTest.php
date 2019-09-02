<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

class PHPAccessControl_Property_ValueDescription_GreaterThanTest extends PHPUnit_Framework_TestCase
{
	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isSatisfiedByValueInRange()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$this->assertTrue($gt2->isSatisfiedBy(3));
	}

	/**
	 * @test
	 */
	public function isNotSatisfiedByValueLessThan()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$this->assertFalse($gt2->isSatisfiedBy(1));
	}

	// ----- is special case of -----

	/**
	 * @test
	 */
	public function isSpecialCaseOfGreaterThanSpecificationWithHigherRange()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$gt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(1);
		$this->assertTrue($gt2->isSpecialCaseOf($gt1));
		$this->assertFalse($gt1->isSpecialCaseOf($gt2));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfEqualIfEqualWithinRange()
	{
		$eq1 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(1);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$gt0 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(0);
		$this->assertTrue($gt0->isGeneralizationOf($eq1));
		$this->assertFalse($gt2->isGeneralizationOf($eq1));
	}

	// ----- not -----

	/**
	 * @test
	 */
	public function notLesserThanIsEqualsOrLesserThan()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$this->assertTrue($gt2->not()->isEqualTo($eq2->lOr($lt2)));
	}
}