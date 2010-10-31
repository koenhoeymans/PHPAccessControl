<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Situation\Situation;

class InMemoryPermissionList implements PermissionList
{
	private $situations = array();

	public function allow(Situation $situation)
	{
		$this->situations[serialize($situation)] = true;
	}

	public function deny(Situation $situation)
	{
		$this->situations[serialize($situation)] = false;
	}

	public function isAllowed(Situation $situation)
	{
		return isset($this->situations[serialize($situation)])
			? $this->situations[serialize($situation)] : null;
	}

	public function findParents(Situation $situation)
	{
		$matchingSituations = array();

		foreach ($this->situations as $serializedStoredSituation => $allowed)
		{
			$storedSituation = unserialize($serializedStoredSituation);

			if ($situation->isEqualTo($storedSituation))
			{
				continue;
			}

			if (!$situation->isSpecialCaseOf($storedSituation))
			{
				continue;
			}

			$alreadyMoreSpecificMatchFound = false;
			foreach ($matchingSituations as $key => $matchingSituation)
			{
				$matchingspecial = $matchingSituation->isSpecialCaseOf($storedSituation);
				$storedspecial = $storedSituation->isSpecialCaseOf($matchingSituation);
				if ($matchingspecial && !$storedspecial)
				{
					$alreadyMoreSpecificMatchFound = true;
					break;
				}
				if (!$matchingspecial && $storedspecial)
				{
					unset($matchingSituations[$key]);
				}
			}
			if (!$alreadyMoreSpecificMatchFound)
			{
				$matchingSituations[] = $storedSituation;
			}
		}

		return $matchingSituations;
	}

	public function findChildren(Situation $situation)
	{
		$matchingSituations = array();

		foreach ($this->situations as $serializedStoredSituation => $allowed)
		{
			$storedSituation = unserialize($serializedStoredSituation);

			if ($situation->isEqualTo($storedSituation))
			{
				continue;
			}

			if (!$storedSituation->isSpecialCaseOf($situation))
			{
				continue;
			}

			$alreadyLessSpecificMatchFound = false;
			foreach ($matchingSituations as $key => $matchingSituation)
			{
				$storedMostGeneral = $matchingSituation->isSpecialCaseOf($storedSituation);
				$matchingMostGeneral = $storedSituation->isSpecialCaseOf($matchingSituation);
				if ($matchingMostGeneral && !$storedMostGeneral)
				{
					$alreadyLessSpecificMatchFound = true;
					break;
				}
				if ($storedMostGeneral && !$matchingMostGeneral)
				{
					unset($matchingSituations[$key]);
				}
			}
			if (!$alreadyLessSpecificMatchFound)
			{
				$matchingSituations[] = $storedSituation;
			}
		}

		return $matchingSituations;
	}
}