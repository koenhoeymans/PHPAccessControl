<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * Compound specification. Multiple specifications connected by 'and': all
 * specifications must apply.
 * 
 * @package PHPAccessControl
 */
class LogicalAnd extends GenericSpecification
{
	/**
	 * All specifications the 'and' consists of.
	 * 
	 * @var array
	 */
	private $components;

	/**
	 * Constructs a logical and, consisting of a variable number of specifications. 
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
	 * @see PHPAccessControl\Specification.GenericSpecification::lAnd()
	 */
	public function lAnd(Specification $specification)
	{
		$reflectionClass = new \ReflectionClass(__CLASS__);
		$arguments = array_merge($this->components, array($specification));
		$instance = $reflectionClass->newInstanceArgs($arguments);
		return $instance;
	}

	/**
	 * The negation of (A and B) is (not A or not B). This returns a
	 * LogicalOr with all parts from the LogicalAnd where all parts are negated.
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::not()
	 * @return LogicalOr
	 */
	public function not()
	{
		$reflectionClass = new \ReflectionClass('\\PHPAccessControl\\Specification\\LogicalOr');
		$arguments = array();
		foreach($this->components as $component)
		{
			$arguments[] = $component->not();
		}
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
			if (!$component->isSatisfiedBy($candidate))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * @todo Implementation is wrong. LogicalAnd is special case of other LogicalAnd
	 * if each component is a special case of the other LogicalAnd (or if each is
	 * special case of each component of other LogicalAnd?).
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::isSpecialCaseOfLogicalAnd()
	 */
	protected function isSpecialCaseOfLogicalAnd(LogicalAnd $logicalAnd)
	{
		foreach ($this->components as $component)
		{
			$componentIsSpecialCase = false;
			foreach ($logicalAnd->components as $otherComponent)
			{
				if ($component->isSpecialCaseOf($otherComponent))
				{
					$componentIsSpecialCase = true;
					break;
				}
			}
			if (!$componentIsSpecialCase)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * A LogicalAnd is Generalization of other LogicalAnd
	 * if the other is a special case.
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::isGeneralizationOfLogicalAnd()
	 */
	protected function isGeneralizationOfLogicalAnd(LogicalAnd $lAnd)
	{
		return $lAnd->isSpecialCaseOf($this);
	}

	/**
	 * LogicalAnd is special case of LogicalOr if at least component is a special case
	 * of the LogicalOr.
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::isSpecialCaseOfLogicalOr()
	 */
	protected function isSpecialCaseOfLogicalOr(LogicalOr $lOr)
	{
		foreach ($this->components as $component)
		{
			if ($component->isSpecialCaseOf($lOr))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * LogicalAnd is a generalization of a LogicalOr if every component is a
	 * generalization of the LogicalOr.
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::isGeneralizationOfLogicalOr()
	 */
	protected function isGeneralizationOfLogicalOr(LogicalOr $lOr)
	{
		foreach ($this->components as $component)
		{
			if (!$component->isGeneralizationOf($lOr))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Is a special case of a LeafSpecification if every component is a
	 * special case of the LeafSpecification.
	 * 
	 * @param Specification $specification
	 * @return boolean
	 */
	protected function isSpecialCaseOfLeafSpecification(Specification $specification)
	{
		foreach ($this->components as $component)
		{
			if ($component->isSpecialCaseOf($specification))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Is a generalization of a LeafSpecification if every component is a
	 * generalization of the LeafSpecification.
	 * 
	 * @param Specification $specification
	 */
	protected function isGeneralizationOfLeafSpecification(Specification $specification)
	{
		foreach ($this->components as $component)
		{
			if (!$component->isGeneralizationOf($specification))
			{
				return false;
			}
		}
		return true;
	}
}