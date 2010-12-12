<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification\ValueBoundSpecification;

/**
 * Specifies that a value must be greater than a certain value.
 * 
 * @package PHPAccessControl
 */
class GreaterThan extends ValueBoundSpecification
{
	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::not()
	 */
	public function not()
	{
		$eq = new Equals($this->value);
		$lt = new LesserThan($this->value);
		return $eq->lOr($lt);
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($value)
	{
		return $value > $this->value;
	}

	/**
	 * Is this greater than a special case of another greater than?
	 * 
	 * @param GreaterThan $gt
	 * @return boolean
	 */
	protected function isSpecialCaseOfGreaterThan(GreaterThan $gt)
	{
		return $gt->value <= $this->value;
	}

	/**
	 * Is this greater than a generalization of another greater than?
	 * 
	 * @param GreaterThan $gt
	 * @return boolean
	 */
	protected function isGeneralizationOfGreaterThan(GreaterThan $gt)
	{
		return $gt->value >= $this->value;
	}

	/**
	 * Is this greater than a generalization of an equals specification?
	 * 
	 * @param Equals $eq
	 * @return boolean
	 */
	protected function isGeneralizationOfEquals(Equals $eq)
	{
		return $eq->isSpecialCaseOf($this);
	}
}