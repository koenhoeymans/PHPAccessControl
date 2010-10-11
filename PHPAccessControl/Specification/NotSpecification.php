<?php

namespace PHPAccessControl\Specification;

class NotSpecification extends GenericSpecification
{
	private $component;

	public function __construct(Specification $specification)
	{
		$this->component = $specification;
	}

	public function not()
	{
		return $this->component;
	}

	public function isSatisfiedBy($candidate)
	{
		return !$this->component->isSatisfiedBy($candidate);
	}

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