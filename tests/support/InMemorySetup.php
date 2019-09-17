<?php

namespace PHPAccessControl;

class InMemorySetup
{
    public static function create()
    {
        $specificationInheritanceList = new \PHPAccessControl\Specification\InMemoryInheritanceList();

        $situationStore = new \PHPAccessControl\Situation\InMemorySituationStore();

        $ruleList = new \PHPAccessControl\Rule\InMemoryRuleList();
        $ruleFinder = new \PHPAccessControl\Rule\SimpleRuleFinder($ruleList);

        $permissionResolver = new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver($ruleFinder);
        $conditionResolver = new \PHPAccessControl\AccessControl\AcoConditionResolver(
            $permissionResolver,
            $situationStore
        );

        $ruleList->addObserver($situationStore);
 
        return new \PHPAccessControl\PHPAccessControl(
            $permissionResolver,
            $conditionResolver,
            $ruleList,
            $specificationInheritanceList
        );
    }
}
