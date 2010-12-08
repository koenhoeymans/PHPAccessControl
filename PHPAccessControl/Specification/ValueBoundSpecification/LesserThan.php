<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification\ValueBoundSpecification;

/**
 * Lesser than specification. Specifies that a value must be lesser than
 * a given value.
 * 
 * @package PHPAccessControl
 *
 */
class LesserThan extends ValueBoundSpecification
{
	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::not()
	 */
	public function not()
	{
		$eq = new Equals($this->value);
		$gt = new GreaterThan($this->value);
		return $eq->lOr($gt);
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($value)
	{
		return $value < $this->value;
	}

	/**
	 * Is this lesser than a special case of another lesser than?
	 * 
	 * @param LesserThan $lt
	 * @return boolean
	 */
	protected function isSpecialCaseOfLesserThan(LesserThan $lt)
	{
		return $this->value <= $lt->value;
	}

	/**
	 * Is this lesser than a generalization of another lesser than?
	 * 
	 * @param LesserThan $lt
	 * @return boolean
	 */
	protected function isGeneralizationOfLesserThan(LesserThan $lt)
	{
		return $lt->value <= $this->value;
	}

	/**
	 * Is this lesser than a generalization of an equals specification?
	 * 
	 * @param Equals $eq
	 * @return boolean
	 */
	protected function isGeneralizationOfEquals(Equals $eq)
	{
		return $eq->isSpecialCaseOf($this);
	}
}