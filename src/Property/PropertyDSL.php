<?php

namespace PHPAccessControl\Property;

class PropertyDSL extends Property
{
	public function equals($int)
	{
		$specification =
			new \PHPAccessControl\Specification\ValueBoundSpecification\Equals($int);
		return $this->createPropertyWith($specification);
	}

	public function lesserThan($int)
	{
		$specification =
			new \PHPAccessControl\Specification\ValueBoundSpecification\LesserThan($int);
		return $this->createPropertyWith($specification);
	}

	public function greaterThan($int)
	{
		$specification =
			new \PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan($int);
		return $this->createPropertyWith($specification);
	}

	protected function createPropertyWith(\PHPAccessControl\Specification\Specification $specification)
	{
		if ($this->specification !== null)
		{
			$specification = $this->specification->lAnd();
		}
		return new self($this->name, $specification);
	}
}