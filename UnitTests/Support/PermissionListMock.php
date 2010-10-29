<?php

namespace PHPAccessControl\UnitTests\Support;

use PHPAccessControl\Situation\Situation;

class PermissionListMock implements \PHPAccessControl\AccessControl\PermissionList
{
	private $permissions = array();

	private $parents = array();

	private $children = array();

	public function allow(Situation $situation)
	{
		$this->permissions[serialize($situation)] = true;
	}

	public function deny(Situation $situation)
	{
		$this->permissions[serialize($situation)] = false;
	}

	public function isAllowed(Situation $situation)
	{
		if (isset($this->permissions[serialize($situation)]))
		{
			return $this->permissions[serialize($situation)];
		}
		return null;
	}

	public function addParent(Situation $situation, Situation $parentSituation)
	{
		$this->parents[serialize($situation)][] = $parentSituation;
		$this->children[serialize($parentSituation)][] = $situation;
	}

	public function findParents(Situation $situation)
	{
		if (isset($this->parents[serialize($situation)]))
		{
			return $this->parents[serialize($situation)];
		}
		return array();
	}

	public function findChildren(Situation $situation)
	{
		if (isset($this->children[serialize($situation)]))
		{
			return $this->children[serialize($situation)];
		}
		return array();
	}
}