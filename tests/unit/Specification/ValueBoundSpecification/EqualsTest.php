<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

class PHPAccessControl_Specification_ValueBoundSpecification_EqualsTest
	extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function isSatisfiedByValueItContains()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$this->assertTrue($eq2->isSatisfiedBy(2));
	}

	/**
	 * @test
	 */
	public function isNotSatisfiedByDifferentValue()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$this->assertFalse($eq2->isSatisfiedBy(3));
	}

	/**
	 * @test
	 */
	public function isSpecialCaseofEqualIfSameValue()
	{
		$eq2a = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$eq2b = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$this->assertTrue($eq2a->isSpecialCaseOf($eq2b));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfEqualIfSameValue()
	{
		$eq2a = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$eq2b = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$this->assertTrue($eq2a->isGeneralizationOf($eq2b));
	}

	/**
	 * @test
	 */
	public function isSpecialCaseOfLesserThanIfWithingRange()
	{
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$eq1 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(1);
		$this->assertTrue($eq1->isSpecialCaseOf($lt2));
	}

	/**
	 * @test
	 */
	public function isSpecialCaseOfGreaterThanIfWithingRange()
	{
		$gt0 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(0);
		$eq1 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(1);
		$this->assertTrue($eq1->isSpecialCaseOf($gt0));
	}

	/**
	 * @test
	 */
	public function notEqualsReturnsGreaterThanOrLesserThan()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$this->assertTrue($eq2->not()->isEqualTo($lt2->lOr($gt2)));
	}
}