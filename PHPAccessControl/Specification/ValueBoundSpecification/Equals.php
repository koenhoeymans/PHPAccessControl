<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification\ValueBoundSpecification;

/**
 * A value equals another value.
 * 
 * @package PHPAccessControl
 */
class Equals extends ValueBoundSpecification
{
	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::not()
	 */
	public function not()
	{
		$lt = new LesserThan($this->value);
		$gt = new GreaterThan($this->value);
		return $lt->lOr($gt);
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($value)
	{
		return $value == $this->value;
	}

	/**
	 * Is this equals a special case of another? Can only be the
	 * case if both values are the same.
	 * 
	 * @param Equals $eq
	 */
	protected function isSpecialCaseOfEquals(Equals $eq)
	{
		return $eq->value == $this->value;
	}

	/**
	 * Is this equals a generalization of another?
	 * 
	 * @param Equals $eq
	 */
	protected function isGeneralizationOfEquals(Equals $eq)
	{
		return $eq->value == $this->value;
	}

	/**
	 * Is this a special case of a lesser than specification?
	 * 
	 * @param LesserThan $lt
	 */
	protected function isSpecialCaseOfLesserThan(LesserThan $lt)
	{
		return $lt->isSatisfiedBy($this->value);
	}

	/**
	 * Is this a special case of a greater than specification?
	 * 
	 * @param GreaterThan $gt
	 */
	protected function isSpecialCaseOfGreaterThan(GreaterThan $gt)
	{
		return $gt->isSatisfiedBy($this->value);
	}
}