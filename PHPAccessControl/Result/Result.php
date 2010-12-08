<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Result;

use PHPAccessControl\Specification\Specification;

/**
 * Contains the result of the access request.
 * 
 * @package PHPAccessControl
 */
class Result
{
	/**
	 * Is it allowed?
	 * 
	 * @var boolean
	 */
	private $allowed;

	/**
	 * Are their access conditions?
	 * 
	 * @var Specification|null
	 */
	private $specification;

	/**
	 * Constructs the result of the access request: whether it is allowed
	 * and optional whether there are any conditions under which access
	 * is granted.
	 * 
	 * @param boolean $allowed
	 * @param Specification $specification
	 */
	public function __construct($allowed, Specification $specification = null)
	{
		$this->allowed = (bool) $allowed;
		$this->specification = $specification;
	}

	/**
	 * Is access allowed?
	 * 
	 * @return boolean
	 */
	public function isAllowed()
	{
		return $this->allowed ?: false;
	}

	/**
	 * Is access conditional?
	 * 
	 * @return boolean
	 */
	public function isConditional()
	{
		return ($this->specification === null) ? false: true;
	}

	/**
	 * Get conditions that allow access.
	 * 
	 * @return Specification
	 */
	public function getAllowed()
	{
		return $this->specification;
	}
}