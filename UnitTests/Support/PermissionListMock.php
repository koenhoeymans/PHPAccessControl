<?php

namespace PHPAccessControl\UnitTests\Support;

class PermissionListMock implements \PHPAccessControl\AccessControl\PermissionList
{
	private $permissions = array();

	private $parents = array();

	private $children = array();

	public function allow(\PHPAccessControl\Situation\Situation $situation)
	{
		$this->permissions[serialize($situation)] = true;
	}

	public function deny(\PHPAccessControl\Situation\Situation $situation)
	{
		$this->permissions[serialize($situation)] = false;
	}

	public function isAllowed(\PHPAccessControl\Situation\Situation $situation)
	{
		if (isset($this->permissions[serialize($situation)]))
		{
			return $this->permissions[serialize($situation)];
		}
		return null;
	}

	public function addParent(\PHPAccessControl\Situation\Situation $situation, \PHPAccessControl\Situation\Situation $parentSituation)
	{
		$this->parents[serialize($situation)][] = $parentSituation;
		$this->children[serialize($parentSituation)][] = $situation;
	}

	public function findParents(\PHPAccessControl\Situation\Situation $situation)
	{
		if (isset($this->parents[serialize($situation)]))
		{
			return $this->parents[serialize($situation)];
		}
		return array();
	}

	public function findChildren(\PHPAccessControl\Situation\Situation $situation)
	{
		if (isset($this->children[serialize($situation)]))
		{
			return $this->children[serialize($situation)];
		}
		return array();
	}
}