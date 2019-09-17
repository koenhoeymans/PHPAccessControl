<?php

namespace PHPAccessControl\Situation;

interface SituationStore
{
    public function add(Situation $situation);

    public function getChildren(Situation $situation);
}
