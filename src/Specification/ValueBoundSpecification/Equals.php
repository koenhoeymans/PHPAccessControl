<?php

namespace PHPAccessControl\Specification\ValueBoundSpecification;

class Equals extends ValueBoundSpecification
{
	public function not()
	{
		$lt = new LesserThan($this->value);
		$gt = new GreaterThan($this->value);
		return $lt->lOr($gt);
	}

	public function isSatisfiedBy($value)
	{
		return $value == $this->value;
	}

	public function isSpecialCaseOfEquals(Equals $eq)
	{
		return $eq->value == $this->value;
	}

	public function isGeneralizationOfEquals(Equals $eq)
	{
		return $eq->value == $this->value;
	}

	public function isSpecialCaseOfLesserThan(LesserThan $lt)
	{
		return $lt->isSatisfiedBy($this->value);
	}

	public function isSpecialCaseOfGreaterThan(GreaterThan $gt)
	{
		return $gt->isSatisfiedBy($this->value);
	}
}