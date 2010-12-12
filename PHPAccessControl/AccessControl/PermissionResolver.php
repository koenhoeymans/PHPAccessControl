<?php

namespace PHPAccessControl\AccessControl;

use PHPAccessControl\Situation\Situation;
use PHPAccessControl\Specification\Specification;

/**
 * Responsible for determining whether a situation is allowed to happen.
 * 
 * @package PHPAccessControl
 */
interface PermissionResolver
{
	/**
	 * Determines whether a situation is allowed to happen by looking at
	 * permissions granted to parent situations or the situation itself. It
	 * doesn't take permissions of children into account and access is therefor
	 * unconditional.
	 * 
	 * @param	Situation $situation
	 * @return	boolean
	 */
	public function isAllowed(Situation $situation);

	/**
	 * Build the conditions that are necessary for a situation to be allowed
	 * to happen. If there are no conditions it will return null.
	 * 
	 * @param	Situation $situation
	 * @return	Specification|null
	 */
	public function buildAccessConditionsFor(Situation $situation);
}