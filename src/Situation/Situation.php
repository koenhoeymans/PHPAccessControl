<?php

namespace PHPAccessControl\Situation;

use PHPAccessControl\Action\Action;
use PHPAccessControl\Specification\Specification;

class Situation extends \PHPAccessControl\Specification\LeafSpecification
{
    private $subject;

    private $action;

    private $object;

    public function __construct(
        Specification $subject,
        Action $action,
        Specification $object
    ) {
        $this->subject = $subject;
        $this->action = $action;
        $this->object = $object;
    }

    public function getAco()
    {
        return $this->object;
    }

    public function isSpecialCaseOfSituation(Situation $situation)
    {
        $subjectSpecialCase = $this->subject->isSpecialCaseOf($situation->subject);
        $actionSame = $this->action->isSpecialCaseOf($situation->action);
        $objectSpecialCase = $this->object->isSpecialCaseOf($situation->object);
        return $subjectSpecialCase && $actionSame && $objectSpecialCase;
    }

    public function isGeneralizationOfSituation(Situation $situation)
    {
        return $situation->isSpecialCaseOf($this);
    }
}
