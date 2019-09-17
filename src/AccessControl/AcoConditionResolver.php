<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Situation\SituationStore;
use PHPAccessControl\Situation\Situation;

class AcoConditionResolver implements ConditionResolver
{
    private $permissionResolver;

    private $situationStore;

    public function __construct(
        PermissionResolver $permissionResolver,
        SituationStore $situationStore
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->situationStore = $situationStore;
    }

    public function buildAccessConditionsFor(Situation $situation)
    {
        $situationAllowed = $this->permissionResolver->isAllowedByInheritance($situation);
        return $this->buildAccessConditionsRecursively($situation, $situationAllowed);
    }

    private function buildAccessConditionsRecursively(Situation $situation, $situationAllowed)
    {
        $conditions = null;
        $childSituations = $this->situationStore->getChildren($situation);

        foreach ($childSituations as $childSituation) {
            $condition = null;
            $childSituationAllowed = $this->permissionResolver->isAllowedByInheritance($childSituation);
            $childConditions = $this->buildAccessConditionsRecursively($childSituation, $childSituationAllowed);

            // find the condition from the childSituation
            if (($situationAllowed !== $childSituationAllowed) && ($childSituationAllowed !== null)) {
                $condition = $childSituation->getAco();
                if ($situationAllowed) {
                    $condition = $condition->not();
                }
                if ($condition && $childConditions) {
                    $condition = ($childSituationAllowed)
                        ? $condition->lAnd($childConditions) : $condition->lOr($childConditions);
                }
            } else {
                $condition = $childConditions;
            }

            // create the conditions so far
            if ($conditions && $condition) {
                $conditions = ($situationAllowed)
                        ? $conditions->lAnd($condition) : $conditions->lOr($condition);
            }
            if ($conditions === null) {
                $conditions = $condition;
            }
        }

        return $conditions;
    }
}
