<?php

namespace PHPAccessControl\AccessControledObject;

use PHPAccessControl\Property\PropertyList;
use	PHPAccessControl\Property\Property;
use	PHPAccessControl\Specification\Specification;

class Aco extends \PHPAccessControl\Specification\LeafSpecification
{
	const ANY_ACO = 'any aco';

	private $name;

	private $properties;

	public function __construct($name = self::ANY_ACO)
	{
		$this->name = $name;
		$this->properties = new PropertyList();
	}

	public function with(Specification $property)
	{
		$copy = clone $this;
		$copy->properties->add($property);
		return $copy;
	}

	public function isSpecialCaseOfAco(Aco $aco)
	{
		if ($aco->name === self::ANY_ACO)
		{
			return true;
		}

		if ($this->name !== $aco->name)
		{
			return false;
		}

		// every property of $aco must be a generalization of $this properties
		foreach ($aco->properties as $otherProperty)
		{
			$otherPropertyGeneralization = false;
			foreach ($this->properties as $ownProperty)
			{
				if ($otherProperty->isGeneralizationOf($ownProperty))
				{
					$otherPropertyGeneralization = true;
					break;
				}
			}
			if (!$otherPropertyGeneralization)
			{
				return false;
			}
		}

		return true;
	}

	public function isGeneralizationOfAco(Aco $aco)
	{
		return $aco->isSpecialCaseOf($this);
	}
}