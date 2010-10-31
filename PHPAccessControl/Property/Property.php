<?php

namespace PHPAccessControl\Property;

use PHPAccessControl\Specification\Specification;

class Property extends \PHPAccessControl\Specification\LeafSpecification
{
	protected $name;

	protected $specification;

	public function __construct($name, Specification $specification = null)
	{
		$this->name = $name;
		$this->specification = $specification;
	}

	public static function named($name)
	{
		$class = get_called_class();
		return new $class($name);
	}

	protected function isSpecialCaseOfProperty(Property $property)
	{
		if ($this->name !== $property->name)
		{
			return false;
		}
		if (!$property->specification)
		{
			return true;
		}
		if (!$this->specification)
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