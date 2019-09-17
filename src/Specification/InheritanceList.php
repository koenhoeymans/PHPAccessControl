<?php

namespace PHPAccessControl\Specification;

interface InheritanceList
{
    public function addParent(Specification $parent, Specification $child);

    public function getParents(Specification $aco);

    public function getParentsRecursively(Specification $aco);
}
