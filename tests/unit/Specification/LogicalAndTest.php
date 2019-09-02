<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

class PHPAccessControl_Specification_LogicalAndTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function acceptsAnyNumberOfSpecificationAsParts()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$gt3 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(3);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$lAndA = $gt2->lAnd($gt3)->lAnd($gt4);
		$lAndB = new \PHPAccessControl\Specification\LogicalAnd($gt2, $gt3, $gt4);
		$this->assertTrue($lAndB->isEqualTo($lAndA));
		$this->assertTrue($lAndB->isSpecialCaseOf($gt4));
	}

	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isSatisfiedIfContainingAreSatisfied()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2AndLt4 = $gt2->lAnd($lt4);
		$this->assertTrue($gt2AndLt4->isSatisfiedBy(3));
	}

	// ----- is equal to -----

	/**
	 * @test
	 */
	public function isEqualToSame()
	{
		$eq6 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$this->assertTrue($eq6->lAnd($lt4)->isEqualTo($eq6->lAnd($lt4)));
		$this->assertTrue($eq6->lAnd($lt4)->isEqualTo($lt4->lAnd($eq6)));
	}

	// ----- is special case of -----

	/**
	 * @test
	 */
	public function isSpecialCaseOfLogicalAndIfEveryPartSpecialCaseOfAtLeastOneOtherPart()
	{
		$eq6 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$lt3 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(3);
		$this->assertTrue($eq6->lAnd($lt3)->isSpecialCaseOf($lt4->lAnd($eq6)));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfLogicalAndIfOtherLogicalAndIsSpecialCase()
	{
		$eq6 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$lt3 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(3);
		$this->assertTrue($eq6->lAnd($lt4)->isGeneralizationOf($lt3->lAnd($eq6)));
	}

	/**
	 * @test
	 */
	public function isSpecialCaseOfLogicalOrIfOnePartIsSpecialCaseOfAtLeastOnPartOfLogicalOr()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$eq6 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$lt3 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(3);
		$this->assertTrue($eq2->lAnd($lt3)->isSpecialCaseOf($eq6->lOr($lt4)));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfLogicalOrIfAllPartsAreGeneralizationsOfLogicalOr()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$eq3 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(3);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(1);
		$this->assertTrue($gt1->lAnd($lt4)->isGeneralizationOf($eq2->lOr($eq3)));
	}

	/**
	 * @test
	 */
	public function isSpecialCaseOfLeafSpecificationIfOneOfTheContainingIsSpecialCase()
	{
		$eq6 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$this->assertTrue($eq6->lAnd($lt4)->isSpecialCaseOf($eq6));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfLeafSpecificationIfLeafSpecificationIsSpecialCaseOfAllParts()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$gt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(1);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$this->assertTrue($gt1->lAnd($lt4)->isGeneralizationOf($eq2));
	}

	/**
	 * @test
	 */
	public function eq4AndGt4IsSpecialCaseOfEq4()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$this->assertTrue($eq4->lAnd($gt4)->isSpecialCaseOf($eq4));
	}

	/**
	 * @test
	 */
	public function eq4OrGt4AndEq2OrLt2IsSpecialCaseOfEq4OrGt4()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$this->assertTrue($eq4->lOr($gt4)->lAnd($eq2->lOr($lt2))->isSpecialCaseOf($eq4->lOr($gt4)));
	}

	/**
	 * @test
	 */
	public function NotLt4AndNotGt2IsSpecialCaseOfLt4OrGt2()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notLt4AndNotGt2 = $lt4->not()->lAnd($gt2->not());
		$this->assertTrue($notLt4AndNotGt2->isSpecialCaseOf($lt4->lOr($gt2)));
	}

	/**
	 * @test
	 */
	public function NotLt4AndNotGt2IsSpecialCaseOfNotBothLt4OrGt2()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notLt4AndNotGt2 = $lt4->not()->lAnd($gt2->not());
		$this->assertTrue($notLt4AndNotGt2->isSpecialCaseOf($lt4->lOr($gt2)->not()));
	}

	// ----- not -----

	/**
	 * @test
	 */
	public function notBothLt4AndGt2IsEqualToNotLt4OrNotGt2()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notBothLt4AndGt2 = $lt4->lAnd($gt2)->not();
		$notLt4OrNotGt2 = $lt4->not()->lOr($gt2->not());
		$this->assertTrue($notBothLt4AndGt2->isEqualTo($notLt4OrNotGt2));
		$this->assertTrue($notLt4OrNotGt2->isEqualTo($notBothLt4AndGt2));
	}
}