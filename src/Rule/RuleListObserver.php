<?php

namespace PHPAccessControl\Rule;

interface RuleListObserver
{
    public function notifyRuleAdded(SituationBasedRule $rule);
}
