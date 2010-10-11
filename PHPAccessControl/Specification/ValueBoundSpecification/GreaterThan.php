<?php

namespace PHPAccessControl\Specification\ValueBoundSpecification;

class GreaterThan extends ValueBoundSpecification
{
	public function not()
	{
		$eq = new Equals($this->value);
		$lt = new LesserThan($this->value);
		return $eq->lOr($lt);
	}

	public function isSatisfiedBy($value)
	{
		return $value > $this->value;
	}

	public function isSpecialCaseOfGreaterThan(GreaterThan $gt)
	{
		return $gt->value <= $this->value;
	}

	public function isGeneralizationOfGreaterThan(GreaterThan $gt)
	{
		return $gt->value >= $this->value;
	}

	public function isGeneralizationOfEquals(Equals $eq)
	{
		return $eq->isSpecialCaseOf($this);
	}
}