<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * A generic implementation of a specification.
 * 
 * @package PHPAccessControl
 *
 */
abstract class GenericSpecification implements Specification
{
	/** 
	 * @see PHPAccessControl\Specification.Specification::lAnd()
	 */
	public function lAnd(Specification $specification)
	{
		return new LogicalAnd($this, $specification);
	}

	/**
	 * @see PHPAccessControl\Specification.Specification::lOr()
	 */
	public function lOr(Specification $specification)
	{
		return new LogicalOr($this, $specification);
	}

	/**
	 * @see PHPAccessControl\Specification.Specification::not()
	 */
	public function not()
	{
		return new NotSpecification($this);
	}

	/**
	 * @see PHPAccessControl\Specification.Specification::isEqualTo()
	 */
	public function isEqualTo(Specification $specification)
	{
		return $specification->isSpecialCaseOf($this) && $this->isSpecialCaseOf($specification);
	}

	/**
	 * @see PHPAccessControl\Specification.Specification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($candidate)
	{
		return false;
	}

	/**
	 * @see PHPAccessControl\Specification.Specification::isSpecialCaseOf()
	 */
	public function isSpecialCaseOf(Specification $specification)
	{
		$classes = $this->getClasses($specification);
		foreach ($classes as $class)
		{
			$method = 'isSpecialCaseOf' . $class;
			if (is_callable(array($this, $method)))
			{
				return $this->$method($specification);
			}
		}

		return false;
	}

	/**
	 * Is this specification a special case of an And specification? 
	 * 
	 * @param LogicalAnd $lAnd
	 * @return boolean
	 */
	protected function isSpecialCaseOfLogicalAnd(LogicalAnd $lAnd)
	{
		return $lAnd->isGeneralizationOf($this);
	}

	/**
	 * Is this specification a special case of an Or specification?
	 * 
	 * @param LogicalOr $lOr
	 * @return boolean
	 */
	protected function isSpecialCaseOfLogicalOr(LogicalOr $lOr)
	{
		return $lOr->isGeneralizationOf($this);
	}

	/**
	 * Is this specification a special case of a not specification?
	 * 
	 * @param NotSpecification $not
	 * @return boolean
	 */
	protected function isSpecialCaseOfNotSpecification(NotSpecification $not)
	{
		return $not->isGeneralizationOf($this);
	}

	/**
	 * @see PHPAccessControl\Specification.Specification::isGeneralizationOf()
	 */
	public function isGeneralizationOf(Specification $specification)
	{
		$classes = $this->getClasses($specification);
		foreach ($classes as $class)
		{
			$method = 'isGeneralizationOf' . $class;
			if (is_callable(array($this, $method)))
			{
				return $this->$method($specification);
			}
		}

		return false;
	}

	/**
	 * Is this specification a generalization of an Or specification?
	 * 
	 * @param LogicalOr $lOr
	 * @return boolean
	 */
	protected function isGeneralizationOfLogicalOr(LogicalOr $lOr)
	{
		return $lOr->isSpecialCaseOf($this);
	}

	/**
	 * Is this specification a generalization of an And specification?
	 * 
	 * @param LogicalAnd $lAnd
	 * @return boolean
	 */
	protected function isGeneralizationOfLogicalAnd(LogicalAnd $lAnd)
	{
		return $lAnd->isSpecialCaseOf($this);
	}

	/**
	 * Gets the classes the specification implements. Used for isSpecialCaseOf
	 * and IsGeneralizationOf which look at a method like
	 * isSpecialCaseOfImplementingClass. 
	 */
	private function getClasses(Specification $specification)
	{
		$classes = array();
		$namespacedClass = get_class($specification);
		$arr = explode('\\', $namespacedClass);
		$classes[] = end($arr);
		$parentNamespacedClasses = class_parents($namespacedClass);
		foreach ($parentNamespacedClasses as $parentNamespacedClass)
		{
			$arr = explode('\\', $parentNamespacedClass);
			$classes[] = end($arr);
		}
		return $classes;
	}
}