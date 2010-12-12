<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Situation\Situation;

/**
 * Stores permission of Situations in memory.
 * 
 * @package PHPAccessControl
 */
class InMemoryPermissionList implements PermissionList
{
	/**
	 * Array with serialized situation as key, value is boolean (allowed or not)
	 * 
	 * @var array
	 */
	private $situations = array();

	/**
	 * @see PHPAccessControl\AccessControl.PermissionList::allow()
	 */
	public function allow(Situation $situation)
	{
		$this->situations[serialize($situation)] = true;
	}

	/**
	 * @see PHPAccessControl\AccessControl.PermissionList::deny()
	 */
	public function deny(Situation $situation)
	{
		$this->situations[serialize($situation)] = false;
	}

	/**
	 * @see PHPAccessControl\AccessControl.PermissionList::isAllowed()
	 */
	public function isAllowed(Situation $situation)
	{
		return isset($this->situations[serialize($situation)])
			? $this->situations[serialize($situation)] : null;
	}

	/**
	 * @see PHPAccessControl\AccessControl.PermissionList::findParents()
	 */
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

	/**
	 * @see PHPAccessControl\AccessControl.PermissionList::findChildren()
	 */
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