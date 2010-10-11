<?php

namespace PHPAccessControl\Rule;

use PHPAccessControl\Situation\Situation;
use PHPAccessControl\Situation\SituationStore;

class SimpleRuleFinder implements RuleFinder
{
	private $ruleList;

	public function __construct(RuleList $ruleList)
	{
		$this->ruleList = $ruleList;
	}

	public function findMostSpecificMatchingRulesFor(Situation $situation)
	{
		$matchingRules = array();
		foreach ($this->ruleList->getAllRules() as $rule)
		{
			if (!$rule->appliesTo($situation))
			{
				continue;
			}

			// if there's already a more specific matching rule found we don't
			// need to include this one
			$alreadyMoreSpecificMatchFound = false;
			foreach ($matchingRules as $key => $matchingRule)
			{
				$mr = $matchingRule->isSpecialCaseOf($rule);
				$ru = $rule->isSpecialCaseOf($matchingRule);
				if ($mr && !$ru)
				{
					$alreadyMoreSpecificMatchFound = true;
					break;
				}
				if (!$mr && $ru)
				{
					unset($matchingRules[$key]);
				}
			}
			if (!$alreadyMoreSpecificMatchFound)
			{
				$matchingRules[] = $rule;
			}
		}
		return $matchingRules;
	}
}