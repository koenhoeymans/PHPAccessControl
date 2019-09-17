<?php

namespace PHPAccessControl\Result;

use PHPAccessControl\Specification\Specification;

class Result
{
    private $allowed;

    private $specification;

    public function __construct(
        $allowed,
        Specification $specification = null
    ) {
        $this->allowed = (bool) $allowed;
        $this->specification = $specification;
    }

    public function isAllowed()
    {
        return $this->allowed ?: false;
    }

    public function isConditional()
    {
        return ($this->specification === null) ? false: true;
    }

    public function getAllowed()
    {
        return $this->specification;
    }
}
