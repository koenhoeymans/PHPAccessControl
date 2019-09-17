<?php

namespace PHPAccessControl\Specification;

class InMemoryInheritanceList implements InheritanceList
{
    private $relationships = array();

    public function addParent(Specification $parent, Specification $child)
    {
        $key = serialize($child);
        if (!isset($this->relationships[$key])) {
            $this->relationships[$key] = array($parent);
        } elseif (!in_array($parent, $this->relationships[$key])) {
            $this->relationships[$key][] = $parent;
        }
    }

    public function getParents(Specification $aco)
    {
        $key = serialize($aco);
        return isset($this->relationships[$key]) ? $this->relationships[$key] : array();
    }

    public function getParentsRecursively(Specification $aco)
    {
        return $this->getParentsRecursivelyExcludingFound($aco, array($aco));
    }

    private function getParentsRecursivelyExcludingFound(Specification $aco, array $exclude)
    {
        $parents = $this->getParents($aco);
        foreach ($parents as $key => $parent) {
            if (in_array($parent, $exclude)) {
                unset($parents[$key]);
                continue;
            }
            $exclude[] = $parent;
            $grandParents = $this->getParentsRecursivelyExcludingFound($parent, $exclude);
            $parents = array_merge($parents, $this->getParentsRecursivelyExcludingFound($parent, $exclude));
        }
        return $parents;
    }
}
