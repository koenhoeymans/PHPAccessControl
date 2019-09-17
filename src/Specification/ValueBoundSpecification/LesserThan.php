<?php

namespace PHPAccessControl\Specification\ValueBoundSpecification;

class LesserThan extends ValueBoundSpecification
{
    public function not()
    {
        $eq = new Equals($this->value);
        $gt = new GreaterThan($this->value);
        return $eq->lOr($gt);
    }

    public function isSatisfiedBy($value)
    {
        return $value < $this->value;
    }

    public function isSpecialCaseOfLesserThan(LesserThan $lt)
    {
        return $this->value <= $lt->value;
    }

    public function isGeneralizationOfLesserThan(LesserThan $lt)
    {
        return $lt->value <= $this->value;
    }

    public function isGeneralizationOfEquals(Equals $eq)
    {
        return $eq->isSpecialCaseOf($this);
    }
}
