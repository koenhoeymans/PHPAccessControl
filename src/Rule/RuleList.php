<?php

namespace PHPAccessControl\Rule;

interface RuleList
{
    public function addObserver(RuleListObserver $observer);

    public function addRule(SituationBasedRule $rule);

    public function getAllRules();
}
