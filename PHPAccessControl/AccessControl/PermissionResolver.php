<?php

namespace PHPAccessControl\AccessControl;

interface PermissionResolver
{
	public function isAllowed(
		\PHPAccessControl\Situation\Situation $situation
	);

	public function buildAccessConditionsFor(
		\PHPAccessControl\Situation\Situation $situation
	);
}