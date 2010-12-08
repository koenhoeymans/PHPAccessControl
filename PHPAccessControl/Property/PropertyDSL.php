<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Property;

/**
 * Utility class to more easily create property specifications.
 * 
 * @package PHPAccessControl
 */
class PropertyDSL extends Property
{
	/**
	 * Property equals number.
	 * 
	 * @param int $int
	 * @preturn PropertyDSL
	 */
	public function equals($int)
	{
		$specification =
			new \PHPAccessControl\Specification\ValueBoundSpecification\Equals($int);
		return $this->createPropertyWith($specification);
	}

	/**
	 * Property is lesser than a value.
	 * 
	 * @param int $int
	 * @return PropertyDSL
	 */
	public function lesserThan($int)
	{
		$specification =
			new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan($int);
		return $this->createPropertyWith($specification);
	}

	/**
	 * Property is greater than a value.
	 * 
	 * @param int $int
	 * @return PropertyDSL
	 */
	public function greaterThan($int)
	{
		$specification =
			new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan($int);
		return $this->createPropertyWith($specification);
	}

	/**
	 * Creates the property with the added specification.
	 * 
	 * @param \PHPAccessControl\Specification\Specification $specification
	 * @return PropertyDSL
	 */
	protected function createPropertyWith(\PHPAccessControl\Specification\Specification $specification)
	{
		if ($this->specification !== null)
		{
			$specification = $this->specification->lAnd($specification);
		}
		return new self($this->name, $specification);
	}
}