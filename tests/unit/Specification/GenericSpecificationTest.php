<?php

namespace PHPAccessControl\Specification;

class GenericSpecificationTest extends \PHPUnit\Framework\TestCase
{
    private $specification1;

    private $specification2;

    public function setup()
    {
        $this->specification1 = $this->getMockForAbstractClass(
            'PHPAccessControl\\Specification\\GenericSpecification'
        );
        $this->specification2 = $this->getMockForAbstractClass(
            'PHPAccessControl\\Specification\\GenericSpecification'
        );
    }

	// ----- is satisfied by -----

	/**
	 * @test
	 */
	public function isNeverSatisfied()
	{
		$this->assertFalse($this->specification1->isSatisfiedBy(1));
	}

	// ----- equal -----

	// ----- Special Case Of, Generalization Of -----

	/**
	 * @test
	 */
	public function seeksMethodIsSpecialCaseOfOtherClassNameWhenComparingSpecialCaseWithOtherSpecification()
	{
		$this->specification1
			->expects($this->any())
			->method('isSpecialCaseOfGenericSpecification')
			->will($this->returnValue(true));
		$this->assertTrue($this->specification1->isSpecialCaseOf($this->specification2));
	}

	/**
	 * @test
	 */
	public function seeksMethodIsGeneralizationOfOtherClassNameWhenComparingSpecialCaseWithOtherSpecification()
	{
		$this->specification1
			->expects($this->any())
			->method('isGeneralizationOfGenericSpecification')
			->will($this->returnValue(true));
		$this->assertTrue($this->specification1->isGeneralizationOf($this->specification2));
	}

	/**
	 * @test
	 */
	public function isNotSpecialCaseOfAnySpecification()
	{
		$this->assertFalse($this->specification1->isSpecialCaseOf($this->specification2));
	}

	/**
	 * @test
	 */
	public function isNotGeneralizationOfAnySpecification()
	{
		$this->assertFalse($this->specification1->isGeneralizationOf($this->specification2));
	}
}
