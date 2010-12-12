<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification;

/**
 * Specification blueprint.
 * 
 * @package PHPAccessControl
 *
 */
interface Specification
{
	/**
	 * Creates a compound specification. Both specifications must apply.
	 * 
	 * @param Specification $specification
	 * @return LogicalAnd
	 */
	public function lAnd(Specification $specification);

	/**
	 * Creates a compound specification. One or both specifications must apply.
	 * 
	 * @param Specification $specification
	 * @return LogicalOr
	 */
	public function lOr(Specification $specification);

	/**
	 * Negation of a specification.
	 * 
	 * @return Specification
	 */
	public function not();

	/**
	 * A specification is equal to another specification if both describe the same
	 * objects.
	 * 
	 * @param Specification $specification
	 * @return boolean
	 */
	public function isEqualTo(Specification $specification);

	/**
	 * A specification describes a number of objects. A candidate is either one of
	 * those or not (satisfied by the description or not).
	 * 
	 * @param var $candidate
	 * @return boolean
	 */
	public function isSatisfiedBy($candidate);

	/**
	 * A specification is a special case of another specification if all the objects
	 * it describes are also described by the other specification.
	 * 
	 * @param Specification $specification
	 * @return booleann
	 */
	public function isSpecialCaseOf(Specification $specification);

	/**
	 * A specification is a generalization of another specification if it describes all
	 * the objects (or more) the other specification describes.
	 * 
	 * @param Specification $specification
	 * @return boolean
	 */
	public function isGeneralizationOf(Specification $specification);
}