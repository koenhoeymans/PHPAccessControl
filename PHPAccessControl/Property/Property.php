<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Property;

use PHPAccessControl\Specification\Specification;

/**
 * A property describes an object.
 * 
 * @package PHPAccessControl
 */
class Property extends \PHPAccessControl\Specification\LeafSpecification
{
	/**
	 * The name of the specified property.
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * The specification of the property.
	 * 
	 * @var Specification
	 */
	protected $specification;

	/**
	 * Constructs a property.
	 * 
	 * @param string $name
	 * @param Specification $specification
	 */
	public function __construct($name, Specification $specification = null)
	{
		$this->name = $name;
		$this->specification = $specification;
	}

	/**
	 * Higher level language for constructing a property
	 * 
	 * @param string $name
	 */
	public static function named($name)
	{
		$class = get_called_class();
		return new $class($name);
	}

	/**
	 * Is this property a special case of another property?
	 * 
	 * @param Property $property
	 * @return boolean
	 */
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

	/**
	 * Is this property a generalization of another property?
	 * 
	 * @param Property $property
	 * @return boolean
	 */
	protected function isGeneralizationOfProperty(Property $property)
	{
		return $property->isSpecialCaseOf($this);
	}
}