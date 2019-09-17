<?php

namespace PHPAccessControl;

class CreateRule
{
    public static function allow(
        \PHPAccessControl\Specification\Specification $specification
    ) {
        return new \PHPAccessControl\Rule\SituationBasedRule($specification, true);
    }

    public static function deny(
        \PHPAccessControl\Specification\Specification $specification
    ) {
        return new \PHPAccessControl\Rule\SituationBasedRule($specification, false);
    }
}
