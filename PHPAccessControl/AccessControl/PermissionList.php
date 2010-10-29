<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Situation\Situation;

interface PermissionList
{
	public function allow(Situation $situation);

	public function deny(Situation $situation);

	public function isAllowed(Situation $situation);

	public function findParents(Situation $situation);

	public function findChildren(Situation $situation);
}