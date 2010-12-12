<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * Keeps a list of which specification is a parent/child of which specification.
 * 
 * @package PHPAccessControl
 */
interface InheritanceList
{
	/**
	 * Ads a parent specification to a child specification.
	 * 
	 * @param Specification $parent
	 * @param Specification $child
	 */
	public function addParent(Specification $parent, Specification $child);

	/**
	 * Gets parent specifications of a specification. Only direct parents are
	 * returned (not parents of parents).
	 * 
	 * @param Specification $aco
	 * @return array
	 */
	public function getParents(Specification $aco);

	/**
	 * Gets all parents, including parents of parents.
	 * 
	 * @param Specification $aco
	 * @return array
	 */
	public function getParentsRecursively(Specification $aco);
}