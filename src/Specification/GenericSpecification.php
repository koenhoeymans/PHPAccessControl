<?php

namespace PHPAccessControl\Specification;

abstract class GenericSpecification implements Specification
{
	public function lAnd(Specification $specification)
	{
		return new LogicalAnd($this, $specification);
	}

	public function lOr(Specification $specification)
	{
		return new LogicalOr($this, $specification);
	}

	public function not()
	{
		return new NotSpecification($this);
	}

	public function isEqualTo(Specification $specification)
	{
		return $specification->isSpecialCaseOf($this) && $this->isSpecialCaseOf($specification);
	}

	public function isSatisfiedBy($candidate)
	{
		return false;
	}

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

	protected function isSpecialCaseOfLogicalAnd(LogicalAnd $lAnd)
	{
		return $lAnd->isGeneralizationOf($this);
	}

	protected function isSpecialCaseOfLogicalOr(LogicalOr $lOr)
	{
		return $lOr->isGeneralizationOf($this);
	}

	protected function isSpecialCaseOfNotSpecification(NotSpecification $not)
	{
		return $not->isGeneralizationOf($this);
	}

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

	protected function isGeneralizationOfLogicalOr(LogicalOr $lOr)
	{
		return $lOr->isSpecialCaseOf($this);
	}

	protected function isGeneralizationOfLogicalAnd(LogicalAnd $lAnd)
	{
		return $lAnd->isSpecialCaseOf($this);
	}

//	protected function isGeneralizationOfNotSpecification(NotSpecification $not)
//	{
//		return $not->isSpecialCaseOf($this);
//	}

	/**
	 * @todo think about changing this
	 * possibly don't look at subclass, only leaf specification, and
	 * have special case / generalization see if inverse from other
	 * specification is available ($this->isSpcialCase(other) -> $ohter->isGen(this) 
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