<?php

namespace PHPAccessControl\AccessControledObject;

use	PHPAccessControl\Specification\Specification;

/**
 * Access Controled Object or aco. A representation of an object that is
 * allowed or denied an action on another object. An aco has a name
 * as the most basic description of the object. If the aco hasn't got a name it is
 * used to represent all objects. The aco can further specify the object
 * by describing its attributes.
 * 
 * @package PHPAccessControl
 */
class Aco extends \PHPAccessControl\Specification\LeafSpecification
{
	/**
	 * Used to describe an aco representing all objects.
	 * 
	 * @var string
	 */
	const ANY_ACO = 'any aco';

	/**
	 * The name of the described object.
	 * 
	 * @var string
	 */
	private $name;

	/**
	 * A list of descriptions of properties of the object described
	 * by the aco.
	 * 
	 * @var array Contains specifications.
	 */
	private $specifications = array();

	/**
	 * If no name is given it defaults to any object.
	 * 
	 * @param string $name
	 */
	public function __construct($name = self::ANY_ACO)
	{
		$this->name = $name;
	}

	/**
	 * Provides higher level constructor syntax for creating
	 * acos: Aco::named(name) vs new Aco(name).
	 * 
	 * @param string $name
	 */
	public static function named($name)
	{
		return new self($name);
	}

	/**
	 * Provides higher level constructor syntax for creating
	 * an aco representing any object.
	 */
	public static function any()
	{
		return new self();
	}

	/**
	 * Makes the description of the object more specific. Returns a new aco with the
	 * description added.
	 * 
	 * @param Specification $specification
	 * @return Aco
	 */
	public function with(Specification $specification)
	{
		$copy = clone $this;
		$copy->specifications[] = $specification;
		return $copy;
	}

	/**
	 * More specific than an Aco or not?
	 * 
	 * @param Aco $aco
	 * @return boolean
	 */
	protected function isSpecialCaseOfAco(Aco $aco)
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
		foreach ($aco->specifications as $otherSpecification)
		{
			$otherSpecificationGeneralization = false;
			foreach ($this->specifications as $ownSpecification)
			{
				if ($otherSpecification->isGeneralizationOf($ownSpecification))
				{
					$otherSpecificationGeneralization = true;
					break;
				}
			}
			if (!$otherSpecificationGeneralization)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * More general than some aco or not?
	 * 
	 * @param Aco $aco
	 * @return boolean
	 */
	protected function isGeneralizationOfAco(Aco $aco)
	{
		return $aco->isSpecialCaseOf($this);
	}
}