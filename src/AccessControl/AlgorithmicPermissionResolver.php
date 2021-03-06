<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Rule\RuleFinder;
use PHPAccessControl\Situation\Situation;

class AlgorithmicPermissionResolver implements PermissionResolver
{
    private $ruleFinder;

    public function __construct(RuleFinder $ruleFinder)
    {
        $this->ruleFinder = $ruleFinder;
    }

    public function isAllowedByInheritance(Situation $situation)
    {
        $allowed = false;
        $rules = $this->ruleFinder->findMostSpecificMatchingRulesFor($situation);
        foreach ($rules as $rule) {
            # allow wins from deny
            if ($rule->isAllowed()) {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }
}
