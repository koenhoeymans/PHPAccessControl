<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * Stores the parent relationships in memory.
 * 
 * @package PHPAccessControl
 *
 */
class InMemoryInheritanceList implements InheritanceList
{
	/**
	 * A list of the relationships. The key is the serialized child
	 * with the value an array of the parents.
	 * 
	 * @var array
	 */
	private $relationships = array();

	/**
	 * @see PHPAccessControl\Specification.InheritanceList::addParent()
	 */
	public function addParent(Specification $parent, Specification $child)
	{
		$key = serialize($child);
		if (!isset($this->relationships[$key]))
		{
			$this->relationships[$key] = array($parent);
		}
		elseif (!in_array($parent, $this->relationships[$key]))
		{
			$this->relationships[$key][] = $parent;
		}
	}

	/**
	 * @see PHPAccessControl\Specification.InheritanceList::getParents()
	 */
	public function getParents(Specification $aco)
	{
		$key = serialize($aco);
		return isset($this->relationships[$key]) ? $this->relationships[$key] : array();
	}

	/**
	 * @see PHPAccessControl\Specification.InheritanceList::getParentsRecursively()
	 */
	public function getParentsRecursively(Specification $aco)
	{
		return $this->_getParentsRecursively($aco, array($aco));
	}

	/**
	 * Gets the parents recursively, using an array with parents that already have
	 * been found so to avoid circular dependencies.
	 * 
	 * @param specification $aco
	 * @param array $exclude
	 * @return array
	 */
	private function _getParentsRecursively(Specification $aco, array $exclude)
	{
		$parents = $this->getParents($aco);
		foreach ($parents as $key => $parent)
		{
			if (in_array($parent, $exclude))
			{
				unset($parents[$key]);
				continue;
			}
			$exclude[] = $parent;
			$grandParents = $this->_getParentsRecursively($parent, $exclude);
			$parents = array_merge($parents, $this->_getParentsRecursively($parent, $exclude));
		}
		return $parents;
	}
}