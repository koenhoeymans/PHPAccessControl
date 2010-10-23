<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Rule\RuleFinder;
use PHPAccessControl\Situation\Situation;
use PHPAccessControl\Situation\SituationStore;

class AlgorithmicPermissionResolver implements PermissionResolver
{
	private $ruleFinder;

	private $situationStore;

	public function __construct(RuleFinder $ruleFinder, SituationStore $situationStore)
	{
		$this->ruleFinder = $ruleFinder;
		$this->situationStore = $situationStore;
	}

	public function isAllowedByInheritance(Situation $situation)
	{
		$allowed = false;
		$rules = $this->ruleFinder->findMostSpecificMatchingRulesFor($situation);
		foreach ($rules as $rule)
		{
			if ($rule->isAllowed()) # allow wins from deny
			{
				$allowed = true;
				break;
			}
		}

		return $allowed;
	}

	public function buildAccessConditionsFor(Situation $situation)
	{
		$situationAllowed = $this->isAllowedByInheritance($situation);
		return $this->buildAccessConditionsRecursively($situation, $situationAllowed);
	}

	private function buildAccessConditionsRecursively(Situation $situation, $situationAllowed)
	{
		$conditions = null;
		$childSituations = $this->situationStore->getChildren($situation);

		foreach ($childSituations as $childSituation)
		{
			$condition = null;
			$childSituationAllowed = $this->isAllowedByInheritance($childSituation);
			$childConditions = $this->buildAccessConditionsRecursively($childSituation, $childSituationAllowed);

			// find the condition from the childSituation
			if (($situationAllowed !== $childSituationAllowed) && ($childSituationAllowed !== null))
			{
				$condition = $childSituation->getAco();
				if ($situationAllowed)
				{
					$condition = $condition->not();
				}
				if ($condition && $childConditions)
				{
					$condition = ($childSituationAllowed)
						? $condition->lAnd($childConditions) : $condition->lOr($childConditions);
				}
			}
			else
			{
				$condition = $childConditions;
			}

			// create the conditions so far
			if ($conditions && $condition)
			{
				$conditions = ($situationAllowed)
						? $conditions->lAnd($condition) : $conditions->lOr($condition);
			}
			if ($conditions === null)
			{
				$conditions = $condition;
			}
		}

		return $conditions;
	}
}