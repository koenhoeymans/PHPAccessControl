<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * Compound specification. Connects multiple specification by 'or'.
 * 
 * @package PHPAccessControl
 */
class LogicalOr extends GenericSpecification
{
	/**
	 * The specifications that make up the 'or'.
	 * 
	 * @var array
	 */
	private $components;

	/**
	 * Constructs an or specification by combining any number of specifications.
	 * 
	 * @param Specification $first
	 * @param Specification $second
	 * @param Specification $third
	 */
	public function __construct(Specification $first, Specification $second, Specification $third = null)
	{
		$arguments = func_get_args();
		foreach ($arguments as $argument)
		{
			if ($argument instanceof Specification)
			{
				$this->components[] = $argument;
			}
		}
	}

	/**
	 * The negation of (A or B) is (not A and not B). Hence it returns a LogicalAnd
	 * with all parts of the LogicalAnd but negated.
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::not()
	 * @return LogicalAnd
	 */
	public function not()
	{
		$reflectionClass = new \ReflectionClass('\\PHPAccessControl\\Specification\\LogicalAnd');
		$arguments = array();
		foreach($this->components as $component)
		{
			$arguments[] = $component->not();
		}
		$instance = $reflectionClass->newInstanceArgs($arguments);
		return $instance;
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::lOr()
	 */
	public function lOr(Specification $specification)
	{
		$reflectionClass = new \ReflectionClass(__CLASS__);
		$arguments = array_merge($this->components, array($specification));
		$instance = $reflectionClass->newInstanceArgs($arguments);
		return $instance;
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($candidate)
	{
		foreach ($this->components as $component)
		{
			if ($component->isSatisfiedBy($candidate))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSpecialCaseOf()
	 */
	public function isSpecialCaseOf(Specification $specification)
	{
		foreach ($this->components as $component)
		{
			if (!$component->isSpecialCaseOf($specification))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Generalization of LogicalOr?
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::isGeneralizationOfLogicalOr()
	 */
	protected function isGeneralizationOfLogicalOr(LogicalOr $lOr)
	{
		foreach ($lOr->components as $otherComponent)
		{
			$isSpecialCaseOfAPart = false;
			foreach ($this->components as $component)
			{
				if ($otherComponent->isSpecialCaseOf($component))
				{
					$isSpecialCaseOfAPart = true;
					break;
				}
			}
			if (!$isSpecialCaseOfAPart)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Generalization of LogicalAnd?
	 * @see PHPAccessControl\Specification.GenericSpecification::isGeneralizationOfLogicalAnd()
	 */
	protected function isGeneralizationOfLogicalAnd(LogicalAnd $lAnd)
	{
		return $lAnd->isSpecialCaseOf($this);
	}

	/**
	 * @param LeafSpecification $specification
	 */
	public function isGeneralizationOfLeafSpecification(LeafSpecification $specification)
	{
		foreach ($this->components as $component)
		{
			if ($specification->isSpecialCaseOf($component))
			{
				return true;
			}
		}
		return false;
	}
}