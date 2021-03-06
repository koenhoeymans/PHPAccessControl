<?php

namespace PHPAccessControl\Property;

class Property extends \PHPAccessControl\Specification\LeafSpecification
{
    protected $name;

    protected $specification;

    public function __construct(
        $name,
        \PHPAccessControl\Specification\Specification $specification = null
    ) {
        $this->name = $name;
        $this->specification = $specification;
    }

    public static function named($name)
    {
        $class = get_called_class();
        return new $class($name);
    }

    private function createPropertyWith(\PHPAccessControl\Specification\Specification $specification)
    {
        if ($this->specification !== null) {
            $specification = $this->specification->lAnd();
        }
        return new self($this->name, $specification);
    }

    protected function isSpecialCaseOfProperty(Property $property)
    {
        if ($this->specification) {
            if (!$this->specification->isSpecialCaseOf($property->specification)) {
                return false;
            }
        }
        return $this->name === $property->name;
    }

    protected function isGeneralizationOfProperty(Property $property)
    {
        return $property->isSpecialCaseOf($this);
    }
}
