<?php

namespace PHPAccessControl\AccessControl;

interface ConditionResolver
{
    public function buildAccessConditionsFor(
        \PHPAccessControl\Situation\Situation $situation
    );
}
