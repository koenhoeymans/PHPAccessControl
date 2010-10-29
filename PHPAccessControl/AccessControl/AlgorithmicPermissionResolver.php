<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\AccessControl\PermissionList;
use PHPAccessControl\Situation\Situation;
use PHPAccessControl\Situation\SituationStore;

class AlgorithmicPermissionResolver implements PermissionResolver
{
	private $permissionList;

	public function __construct(PermissionList $permissionList)
	{
		$this->permissionList = $permissionList;
	}

	public function isAllowed(Situation $situation)
	{
		$allowed = $this->permissionList->isAllowed($situation);

		if ($allowed !== null)
		{
			return $allowed;
		}

		foreach ($this->permissionList->findParents($situation) as $parentSituation)
		{
			if($this->permissionList->isAllowed($parentSituation))
			{
				return true;
			}
		}

		return false;
	}

	public function buildAccessConditionsFor(Situation $situation)
	{
		$situationAllowed = $this->isAllowed($situation);
		return $this->buildAccessConditionsRecursively($situation, $situationAllowed);
	}

	private function buildAccessConditionsRecursively(Situation $situation, $situationAllowed)
	{
		$conditions = null;

		foreach ($this->permissionList->findChildren($situation) as $childSituation)
		{
			$condition = null;
			$childSituationAllowed = $this->isAllowed($childSituation);
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