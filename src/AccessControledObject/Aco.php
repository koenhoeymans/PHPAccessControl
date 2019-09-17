<?php

namespace PHPAccessControl\AccessControledObject;

use PHPAccessControl\Property\PropertyList;
use PHPAccessControl\Property\Property;
use PHPAccessControl\Specification\Specification;

class Aco extends \PHPAccessControl\Specification\LeafSpecification
{
    const ANY_ACO = 'any aco';

    private $name;

    private $specifications = array();

    public function __construct($name = self::ANY_ACO)
    {
        $this->name = $name;
    }

    public static function named($name)
    {
        return new self($name);
    }

    public static function any()
    {
        return new self();
    }

    public function with(Specification $specification)
    {
        $copy = clone $this;
        $copy->specifications[] = $specification;
        return $copy;
    }

    public function isSpecialCaseOfAco(Aco $aco)
    {
        if ($aco->name === self::ANY_ACO) {
            return true;
        }

        if ($this->name !== $aco->name) {
            return false;
        }

        // every property of $aco must be a generalization of $this properties
        foreach ($aco->specifications as $otherSpecification) {
            $otherSpecificationGeneralization = false;
            foreach ($this->specifications as $ownSpecification) {
                if ($otherSpecification->isGeneralizationOf($ownSpecification)) {
                    $otherSpecificationGeneralization = true;
                    break;
                }
            }
            if (!$otherSpecificationGeneralization) {
                return false;
            }
        }

        return true;
    }

    public function isGeneralizationOfAco(Aco $aco)
    {
        return $aco->isSpecialCaseOf($this);
    }

    /**
     * @credit: http://www.php.net/manual/en/language.oop5.cloning.php#87066
     */
    public function __clone()
    {
        foreach ($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }
}
