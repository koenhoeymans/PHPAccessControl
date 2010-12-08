<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\AccessControl\PermissionList;
use PHPAccessControl\Situation\Situation;
use PHPAccessControl\Situation\SituationStore;

/**
 * Resolves permission by building them from the ground up.
 * 
 * @package PHPAccessControl
 */
class AlgorithmicPermissionResolver implements PermissionResolver
{
	/**
	 * @var PermissionList
	 */
	private $permissionList;

	/**
	 * @param PermissionList $permissionList
	 */
	public function __construct(PermissionList $permissionList)
	{
		$this->permissionList = $permissionList;
	}

	/**
	 * @see PHPAccessControl\AccessControl.PermissionResolver::isAllowed()
	 */
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

	/**
	 * @see PHPAccessControl\AccessControl.PermissionResolver::buildAccessConditionsFor()
	 */
	public function buildAccessConditionsFor(Situation $situation)
	{
		$situationAllowed = $this->isAllowed($situation);
		return $this->buildAccessConditionsRecursively($situation, $situationAllowed);
	}

	/**
	 * Access Conditions depend on whether more specific situations are allowed
	 * or denied. Here we travel down the inheritance tree to build up the conditions.
	 * 
	 * @param Situation $situation
	 * @param boolean $situationAllowed
	 * 
	 * @return PHPAccessControl\Specification\Specification | null
	 */
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