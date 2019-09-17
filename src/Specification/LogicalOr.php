<?php

namespace PHPAccessControl\Specification;

class LogicalOr extends GenericSpecification
{
    private $components;

    public function __construct(Specification $first, Specification $second, Specification $third = null)
    {
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            if ($argument instanceof Specification) {
                $this->components[] = $argument;
            }
        }
    }

    public function not()
    {
        $reflectionClass = new \ReflectionClass('\\PHPAccessControl\\Specification\\LogicalAnd');
        $arguments = array();
        foreach ($this->components as $component) {
            $arguments[] = $component->not();
        }
        $instance = $reflectionClass->newInstanceArgs($arguments);
        return $instance;
    }

    public function lOr(Specification $specification)
    {
        $reflectionClass = new \ReflectionClass(__CLASS__);
        $arguments = array_merge($this->components, array($specification));
        $instance = $reflectionClass->newInstanceArgs($arguments);
        return $instance;
    }

    public function isSatisfiedBy($candidate)
    {
        foreach ($this->components as $component) {
            if ($component->isSatisfiedBy($candidate)) {
                return true;
            }
        }
        return false;
    }

    public function isSpecialCaseOf(Specification $specification)
    {
        foreach ($this->components as $component) {
            if (!$component->isSpecialCaseOf($specification)) {
                return false;
            }
        }
        return true;
    }

    protected function isGeneralizationOfLogicalOr(LogicalOr $lOr)
    {
        foreach ($lOr->components as $otherComponent) {
            $isSpecialCaseOfAPart = false;
            foreach ($this->components as $component) {
                if ($otherComponent->isSpecialCaseOf($component)) {
                    $isSpecialCaseOfAPart = true;
                    break;
                }
            }
            if (!$isSpecialCaseOfAPart) {
                return false;
            }
        }
        return true;
    }

    protected function isGeneralizationOfLogicalAnd(LogicalAnd $lAnd)
    {
        return $lAnd->isSpecialCaseOf($this);
    }

    public function isGeneralizationOfLeafSpecification(LeafSpecification $specification)
    {
        foreach ($this->components as $component) {
            if ($specification->isSpecialCaseOf($component)) {
                return true;
            }
        }
        return false;
    }
}
