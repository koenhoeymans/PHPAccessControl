<?php

namespace PHPAccessControl\AccessControl;

interface PermissionResolver
{
	public function isAllowedByInheritance(
		\PHPAccessControl\Situation\Situation $situation
	);
}