<?php

namespace PHPAccessControl\Specification;

class LogicalAnd extends GenericSpecification
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

    public function lAnd(Specification $specification)
    {
        $reflectionClass = new \ReflectionClass(__CLASS__);
        $arguments = array_merge($this->components, array($specification));
        $instance = $reflectionClass->newInstanceArgs($arguments);
        return $instance;
    }

    public function not()
    {
        $reflectionClass = new \ReflectionClass('\\PHPAccessControl\\Specification\\LogicalOr');
        $arguments = array();
        foreach ($this->components as $component) {
            $arguments[] = $component->not();
        }
        $instance = $reflectionClass->newInstanceArgs($arguments);
        return $instance;
    }

    public function isSatisfiedBy($candidate)
    {
        foreach ($this->components as $component) {
            if (!$component->isSatisfiedBy($candidate)) {
                return false;
            }
        }
        return true;
    }

    protected function isSpecialCaseOfLogicalAnd(LogicalAnd $logicalAnd)
    {
        foreach ($this->components as $component) {
            $componentIsSpecialCase = false;
            foreach ($logicalAnd->components as $otherComponent) {
                if ($component->isSpecialCaseOf($otherComponent)) {
                    $componentIsSpecialCase = true;
                    break;
                }
            }
            if (!$componentIsSpecialCase) {
                return false;
            }
        }
        return true;
    }

    protected function isGeneralizationOfLogicalAnd(LogicalAnd $lAnd)
    {
        return $lAnd->isSpecialCaseOf($this);
    }

    protected function isSpecialCaseOfLogicalOr(LogicalOr $lOr)
    {
        foreach ($this->components as $component) {
            if ($component->isSpecialCaseOf($lOr)) {
                return true;
            }
        }
        return false;
    }

    protected function isGeneralizationOfLogicalOr(LogicalOr $lOr)
    {
        foreach ($this->components as $component) {
            if (!$component->isGeneralizationOf($lOr)) {
                return false;
            }
        }
        return true;
    }

    protected function isSpecialCaseOfLeafSpecification(Specification $specification)
    {
        foreach ($this->components as $component) {
            if ($component->isSpecialCaseOf($specification)) {
                return true;
            }
        }
        return false;
    }

    protected function isGeneralizationOfLeafSpecification(Specification $specification)
    {
        foreach ($this->components as $component) {
            if (!$component->isGeneralizationOf($specification)) {
                return false;
            }
        }
        return true;
    }
}
