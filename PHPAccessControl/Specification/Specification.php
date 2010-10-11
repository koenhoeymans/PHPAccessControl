<?php

namespace PHPAccessControl\Specification;

interface Specification
{
	public function lAnd(Specification $specification);

	public function lOr(Specification $specification);

	public function not();

	public function isEqualTo(Specification $specification);

	public function isSatisfiedBy($candidate);

	public function isSpecialCaseOf(Specification $specification);

	public function isGeneralizationOf(Specification $specification);
}