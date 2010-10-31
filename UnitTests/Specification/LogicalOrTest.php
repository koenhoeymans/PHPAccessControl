<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

class PHPAccessControl_Specification_LogicalOrTest	extends PHPUnit_Framework_TestCase
{

	/**
	 * @test
	 */
	public function acceptsAnyNumberOfSpecificationAsParts()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$lt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(1);
		$eq1 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(1);
		$lOrA = $gt2->lOr($lt1)->lOr($eq1);
		$lOrB = new \PHPAccessControl\Specification\LogicalOr($gt2, $lt1, $eq1);
		$this->assertTrue($lOrB->isEqualTo($lOrA));
		$this->assertTrue($lOrB->isGeneralizationOf($eq1));
	}

	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isSatisfiedIfOneOfContainingIsSatisfied()
	{
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2OrLt4 = $gt2->lOr($lt4);
		$this->assertTrue($gt2OrLt4->isSatisfiedBy(7));
	}

	/**
	 * @test
	 */
	public function isNotSatisfiedIfNoneOfTheContainingAreSatisfied()
	{
		$gt6 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt6OrLt4 = $gt6->lOr($lt4);
		$this->assertFalse($gt6OrLt4->isSatisfiedBy(5));
	}

	// ----- is equal to -----

	/**
	 * @test
	 */
	public function isEqualToSame()
	{
		$eq6 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$this->assertTrue($eq6->lOr($lt4)->isEqualTo($eq6->lOr($lt4)));
		$this->assertTrue($eq6->lOr($lt4)->isEqualTo($lt4->lOr($eq6)));
	}

	// ----- is special case of -----

	/**
	 * @test
	 */
	public function isSpecialCaseOfSame()
	{
		$eq6a = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4a = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$eq6b = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(6);
		$lt4b = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$this->assertTrue($eq6a->lOr($lt4a)->isSpecialCaseOf($eq6b->lOr($lt4b)));
		$this->assertTrue($eq6a->lOr($lt4a)->isSpecialCaseOf($lt4b->lOr($eq6b)));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfItsParts()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$this->assertTrue($eq4->lOr($gt4)->isGeneralizationOf($eq4));
		$this->assertTrue($eq4->lOr($gt4)->isGeneralizationOf($gt4));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfOrWithSameParts()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$this->assertTrue($eq4->lOr($gt4)->isGeneralizationOf($eq4->lOr($gt4)));
		$this->assertTrue($eq4->lOr($gt4)->isGeneralizationOf($gt4->lOr($eq4)));
	}

	/**
	 * @test
	 */
	public function isNotGeneralizationOfOrWithDifferentParts()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$eq3 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(3);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$this->assertFalse($eq4->lOr($gt4)->isGeneralizationOf($gt4->lOr($eq3)));
		$this->assertFalse($eq4->lOr($gt4)->isGeneralizationOf($eq3->lOr($gt4)));
	}

	/**
	 * @test
	 */
	public function isNotGeneralizationOfSpecificationWhenNoElementsInCommon()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$eq5 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(5);
		$gt6 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(6);
		$this->assertFalse($eq4->lOr($eq5)->isGeneralizationOf($gt6));
	}

	/**
	 * @test
	 */
	public function eq5OrGt5isSpecialCaseOfGt4AndGt2()
	{
		$eq5 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(5);
		$gt5 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(5);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$this->assertTrue($eq5->lOr($gt5)->isSpecialCaseOf($gt4->lAnd($gt2)));
	}

	/**
	 * @test
	 */
	public function eq5OrGt5isGeneralizationOfGt6AndEq7()
	{
		$eq5 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(5);
		$gt5 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(5);
		$gt6 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(6);
		$eq7 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(7);
		$this->assertTrue($eq5->lOr($gt5)->isGeneralizationOf($gt6->lAnd($eq7)));
	}

	/**
	 * @test
	 */
	public function eq5OrGt2IsSpecialCaseOfEq4OrGt1()
	{
		$eq5 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(5);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$gt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(1);
		$this->assertTrue($eq5->lOr($gt2)->isSpecialCaseOf($eq4->lOr($gt1)));
	}

	/**
	 * @test
	 */
	public function eq4OrGt1IsGeneralizationOfEq5OrGt2()
	{
		$eq5 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(5);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$gt1 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(1);
		$this->assertTrue($eq4->lOr($gt1)->isGeneralizationOf($eq5->lOr($gt2)));
	}

	/**
	 * @test
	 */
	public function isSpecialCaseOfSpecificationIfAllOfTheContainingAreSpecialCases()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$lt6 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(6);
		$this->assertTrue($eq2->lOr($lt4)->isSpecialCaseOf($lt6));
	}

	/**
	 * @test
	 */
	public function isGeneralizationOfSpecificationIfSpecificationSpecialCaseOfAnyOfTheContaining()
	{
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$lt6 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(6);
		$this->assertTrue($eq2->lOr($lt6)->isGeneralizationOf($lt4->lOr($eq2)));
	}

	/**
	 * @test
	 */
	public function lt4OrGt2IsGeneralizationOfNotLt4AndNotGt2()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notLt4AndNotGt2 = $lt4->not()->lAnd($gt2->not());
		$this->assertTrue($lt4->lOr($gt2)->isGeneralizationOf($notLt4AndNotGt2));
	}

	/**
	 * @test
	 */
	public function Eq4OrGt4IsGeneralizationOfEq4OrGt4AndEq2OrLt2()
	{
		$eq4 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(4);
		$eq2 = new \PHPAccessControl\Specification\ValueBoundSpecification\Equals(2);
		$gt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(4);
		$lt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(2);
		$this->assertTrue($eq4->lOr($gt4)->isGeneralizationOf($eq4->lOr($gt4)->lAnd($eq2->lOr($lt2))));
	}

	// ----- not -----

	/**
	 * @test
	 */
	public function notBothLt4OrGt2IsSpecialCaseOfLt4()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notBothLt4OrGt2 = $lt4->lOr($gt2)->not();
		$this->assertTrue($notBothLt4OrGt2->isSpecialCaseOf($lt4));
	}

	/**
	 * @test
	 */
	public function notBothLt4OrGt2IsSpecialCaseOfNotLt4()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notBothLt4OrGt2 = $lt4->lOr($gt2)->not();
		$this->assertTrue($notBothLt4OrGt2->isSpecialCaseOf($lt4->not()));
	}

	/**
	 * @test
	 */
	public function notBothLt4OrGt2IsEqualToNotLt4AndNotGt2()
	{
		$lt4 = new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan(4);
		$gt2 = new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan(2);
		$notBothLt4OrGt2 = $lt4->lOr($gt2)->not();
		$notLt4AndNotGt2 = $lt4->not()->lAnd($gt2->not());
		$this->assertTrue($notBothLt4OrGt2->isSpecialCaseOf($notLt4AndNotGt2));
		$this->assertTrue($notLt4AndNotGt2->isSpecialCaseOf($notBothLt4OrGt2));
		$this->assertTrue($notBothLt4OrGt2->isEqualTo($notLt4AndNotGt2));
	}
}