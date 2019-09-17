<?php

namespace PHPAccessControl\Specification\ValueBoundSpecification;

use PHPAccessControl\Specification\ValueBoundSpecification\Equals;
use PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan;
use PHPAccessControl\Specification\ValueBoundSpecification\LesserThan;

class EqualsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function isSatisfiedByValueItContains()
    {
        $eq2 = new Equals(2);
        $this->assertTrue($eq2->isSatisfiedBy(2));
    }

    /**
     * @test
     */
    public function isNotSatisfiedByDifferentValue()
    {
        $eq2 = new Equals(2);
        $this->assertFalse($eq2->isSatisfiedBy(3));
    }

    /**
     * @test
     */
    public function isSpecialCaseofEqualIfSameValue()
    {
        $eq2a = new Equals(2);
        $eq2b = new Equals(2);
        $this->assertTrue($eq2a->isSpecialCaseOf($eq2b));
    }

    /**
     * @test
     */
    public function isGeneralizationOfEqualIfSameValue()
    {
        $eq2a = new Equals(2);
        $eq2b = new Equals(2);
        $this->assertTrue($eq2a->isGeneralizationOf($eq2b));
    }

    /**
     * @test
     */
    public function isSpecialCaseOfLesserThanIfWithingRange()
    {
        $lt2 = new LesserThan(2);
        $eq1 = new Equals(1);
        $this->assertTrue($eq1->isSpecialCaseOf($lt2));
    }

    /**
     * @test
     */
    public function isSpecialCaseOfGreaterThanIfWithingRange()
    {
        $gt0 = new GreaterThan(0);
        $eq1 = new Equals(1);
        $this->assertTrue($eq1->isSpecialCaseOf($gt0));
    }

    /**
     * @test
     */
    public function notEqualsReturnsGreaterThanOrLesserThan()
    {
        $eq2 = new Equals(2);
        $lt2 = new LesserThan(2);
        $gt2 = new GreaterThan(2);
        $this->assertTrue($eq2->not()->isEqualTo($lt2->lOr($gt2)));
    }
}
