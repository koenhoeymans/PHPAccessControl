<?php

namespace PHPAccessControl\Rule;

use PHPAccessControl\Situation\Situation;

class SituationBasedRule
{
    private $situation;

    private $allowed;

    public function __construct(Situation $situation, $allowed)
    {
        $this->situation = $situation;
        $this->allowed = (bool) $allowed;
    }

    public function isAllowed()
    {
        return $this->allowed;
    }

    public function getSituation()
    {
        return $this->situation;
    }

    public function appliesTo(Situation $situation)
    {
        return $situation->isSpecialCaseOf($this->situation);
    }

    public function isSpecialCaseOf(SituationBasedRule $rule)
    {
        return $this->situation->isSpecialCaseOf($rule->situation);
    }
}
