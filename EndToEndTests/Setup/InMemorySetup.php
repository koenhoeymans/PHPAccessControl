<?php

namespace PHPAccessControl\EndToEndTests\Setup;

class InMemorySetup
{
	public static function create()
	{
		$specificationInheritanceList = new \PHPAccessControl\Specification\InMemoryInheritanceList();

		$situationStore = new \PHPAccessControl\Situation\InMemorySituationStore();

		$ruleList = new \PHPAccessControl\Rule\InMemoryRuleList();
		$ruleFinder = new \PHPAccessControl\Rule\SimpleRuleFinder($ruleList);

		$permissionResolver = new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver(
			$ruleFinder, $situationStore
		);

		$ruleList->addObserver($situationStore);
 
		return new \PHPAccessControl\PHPAccessControl(
			$permissionResolver,
			$ruleList,
			$specificationInheritanceList
		);
	}
}