<?php

namespace PHPAccessControl\Rule;

use PHPAccessControl\Situation\Situation;

interface RuleFinder
{
	public function findMostSpecificMatchingRulesFor(Situation $situation);
}