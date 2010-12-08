<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * Negation of a specification.
 * 
 * @package PHPAccessControl
 */
class NotSpecification extends GenericSpecification
{
	private $component;

	public function __construct(Specification $specification)
	{
		$this->component = $specification;
	}

	/**
	 * Negation of a NotSpecification is the Specification it contains.
	 * 
	 * @see PHPAccessControl\Specification.GenericSpecification::not()
	 */
	public function not()
	{
		return $this->component;
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($candidate)
	{
		return !$this->component->isSatisfiedBy($candidate);
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSpecialCaseOf()
	 */
	public function isSpecialCaseOf(Specification $specification)
	{
		# if component defines own not() we use that
		$notComponent = $this->component->not();
		if ($notComponent != $this)
		{
			return $notComponent->isSpecialCaseOf($specification);
		}
		return !$this->component->isSpecialCaseOf($specification);
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isGeneralizationOf()
	 */
	public function isGeneralizationOf(Specification $specification)
	{
		$notComponent = $this->component->not();
		if ($notComponent != $this)
		{
			return $notComponent->isGeneralizationOf($specification);
		}
		return !$this->component->isGeneralizationOf($specification);
	}
}