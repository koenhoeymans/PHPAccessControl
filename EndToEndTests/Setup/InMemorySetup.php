<?php

namespace PHPAccessControl\EndToEndTests\Setup;

class InMemorySetup
{
	public static function create()
	{
		$specificationInheritanceList = new \PHPAccessControl\Specification\InMemoryInheritanceList();

		$ruleList = new \PHPAccessControl\AccessControl\InMemoryPermissionList();

		$permissionResolver = new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver(
			$ruleList
		);

		return new \PHPAccessControl\PHPAccessControl(
			$permissionResolver,
			$ruleList,
			$specificationInheritanceList
		);
	}
}