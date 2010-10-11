<?php

namespace PHPAccessControl\Property;

class Property extends \PHPAccessControl\Specification\LeafSpecification
{
	protected $name;

	protected $specification;

	public function __construct(
		$name,
		\PHPAccessControl\Specification\Specification $specification = null
	) {
		$this->name = $name;
		$this->specification = $specification;
	}

	private function createPropertyWith(\PHPAccessControl\Specification\Specification $specification)
	{
		if ($this->specification !== null)
		{
			$specification = $this->specification->lAnd();
		}
		return new self($this->name, $specification);
	}

	protected function isSpecialCaseOfProperty(Property $property)
	{
		if ($property->name !== $this->name)
		{
			return false;
		}
		return $this->specification->isSpecialCaseOf($property->specification);
	}

	protected function isGeneralizationOfProperty(Property $property)
	{
		return $property->isSpecialCaseOf($this);
	}
}