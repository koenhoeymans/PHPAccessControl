<?php

namespace PHPAccessControl\Situation;

use PHPAccessControl\Rule\SituationBasedRule;
use PHPAccessControl\Rule\RuleListObserver;

class InMemorySituationStore implements SituationStore, RuleListObserver, \Iterator
{
    private $situations = array();

    public function rewind()
    {
        reset($this->situations);
    }

    public function current()
    {
        return current($this->situations);
    }

    public function key()
    {
        return key($this->situations);
    }

    public function next()
    {
        next($this->situations);
    }

    public function valid()
    {
        return key($this->situations) !== null;
    }

    public function notifyRuleAdded(SituationBasedRule $rule)
    {
        $this->add($rule->getSituation());
    }

    public function add(Situation $situation)
    {
        if (!in_array($situation, $this->situations)) {
            $this->situations[] = $situation;
        }
    }

    public function getChildren(Situation $situation)
    {
        $matchingSituations = array();
        foreach ($this->situations as $storedSituation) {
            if ($situation == $storedSituation) {
                continue;
            }

            if (!$storedSituation->isSpecialCaseOf($situation)) {
                continue;
            }

            $alreadyMoreSpecificMatchFound = false;
            foreach ($matchingSituations as $key => $matchingSituation) {
                $matchingspecial = $matchingSituation->isSpecialCaseOf($storedSituation);
                $storedspecial = $storedSituation->isSpecialCaseOf($matchingSituation);
                if ($matchingspecial && !$storedspecial) {
                    $alreadyMoreSpecificMatchFound = true;
                    break;
                }
                if (!$matchingspecial && $storedspecial) {
                    unset($matchingSituations[$key]);
                }
            }
            if (!$alreadyMoreSpecificMatchFound) {
                $matchingSituations[] = $storedSituation;
            }
        }

        return $matchingSituations;
    }
}
