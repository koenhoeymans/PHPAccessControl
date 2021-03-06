<?php

namespace PHPAccessControl\Specification\ValueBoundSpecification;

use PHPAccessControl\Specification\ValueBoundSpecification\Equals;
use PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan;
use PHPAccessControl\Specification\ValueBoundSpecification\LesserThan;

class LesserThanTest extends \PHPUnit\Framework\TestCase
{
    // ----- is satisfied by -----

    /**
     * @test
     */
    public function isSatisfiedByValueInRange()
    {
        $lt2 = new LesserThan(2);
        $this->assertTrue($lt2->isSatisfiedBy(1));
    }

    /**
     * @test
     */
    public function isNotSatisfiedByValueMoreThan()
    {
        $lt2 = new LesserThan(2);
        $this->assertFalse($lt2->isSatisfiedBy(3));
    }

    // ----- is special case of -----

    /**
     * @test
     */
    public function isSpecialCaseOfLesserThanSpecificationWithLowerRange()
    {
        $lt2 = new LesserThan(2);
        $lt1 = new LesserThan(1);
        $this->assertTrue($lt1->isSpecialCaseOf($lt2));
        $this->assertFalse($lt2->isSpecialCaseOf($lt1));
    }

    /**
     * @test
     */
    public function isGeneralizationOfEqualIfEqualWithinRange()
    {
        $eq1 = new Equals(1);
        $lt2 = new LesserThan(2);
        $lt0 = new LesserThan(0);
        $this->assertTrue($lt2->isGeneralizationOf($eq1));
        $this->assertFalse($lt0->isGeneralizationOf($eq1));
    }

    /**
     * @test
     */
    public function isNotSpecialCaseOfEqual()
    {
        $eq1 = new Equals(1);
        $lt2 = new LesserThan(2);
        $this->assertFalse($lt2->isSpecialCaseOf($eq1));
    }

    // ----- not -----

    /**
     * @test
     */
    public function notLesserThanIsEqualsOrGreaterThan()
    {
        $eq2 = new Equals(2);
        $lt2 = new LesserThan(2);
        $gt2 = new GreaterThan(2);
        $this->assertTrue($lt2->not()->isEqualTo($eq2->lOr($gt2)));
    }
}
