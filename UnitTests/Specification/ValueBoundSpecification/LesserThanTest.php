<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

class PHPAccessControl_Specification_LesserThanTest extends PHPUnit_Framework_TestCase
{
	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isSatisfiedByValueInRange()
	{
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$this->assertTrue($lt2->isSatisfiedBy(1));
	}

	/**
	 * @test
	 */
	public function isNotSatisfiedByValueMoreThan()
	{
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$this->assertFalse($lt2->isSatisfiedBy(3));
	}

	// ----- is special case of -----

	/**
	 * @test
	 */
	public function isSpecialCaseOfLesserThanSpecificationWithLowerRange()
	{
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$lt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(1);
		$this->assertTrue($lt1->isSpecialCaseOf($lt2));
		$this->assertFalse($lt2->isSpecialCaseOf($lt1));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfLesserThanIfLesserThanWithinRange()
	{
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$lt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(1);
		$this->assertFalse($lt1->isGeneralizationOf($lt2));
		$this->assertTrue($lt2->isGeneralizationOf($lt1));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfEqualIfEqualWithinRange()
	{
		$eq1 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(1);
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$lt0 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(0);
		$this->assertTrue($lt2->isGeneralizationOf($eq1));
		$this->assertFalse($lt0->isGeneralizationOf($eq1));
	}

	// ----- not -----

	/**
	 * @test
	 */
	public function notLesserThanIsEqualsOrGreaterThan()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$this->assertTrue($lt2->not()->isEqualTo($eq2->lOr($gt2)));
	}
}