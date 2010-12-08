<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Situation\Situation;

/**
 * The list of permissions for a given situtation.
 * 
 * @package PHPAccessControl
 */
interface PermissionList
{
	/**
	 * Allow a situation to happen.
	 * 
	 * @param Situation $situation
	 */
	public function allow(Situation $situation);

	/**
	 * Denies a situation to happen.
	 * 
	 * @param Situation $situation
	 */
	public function deny(Situation $situation);

	/**
	 * Query whether a situation is allowed to happen. Return NULL if
	 * the situation isn't in the list.
	 * 
	 * @param	Situation $situation
	 * @return	boolean|null
	 */
	public function isAllowed(Situation $situation);

	/**
	 * Finds situations that are less specific. Only direct parents are given (not
	 * parents of those parents). Multiple parents may exist.
	 * 
	 * @param	Situation $situation
	 * @return	array An array with the parent situations.
	 */
	public function findParents(Situation $situation);

	/**
	 * Finds situations that are less specific. Only direct parents are given (not
	 * parents of those parents). Multiple parents may exist.
	 * 
	 * @param	Situation $situationS
	 * @return	array An array with the child situations.
	 */
	public function findChildren(Situation $situation);
}