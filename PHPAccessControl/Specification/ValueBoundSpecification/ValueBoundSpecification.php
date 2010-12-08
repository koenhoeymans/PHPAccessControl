<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Specification\ValueBoundSpecification;

/**
 * Specifications regarding values.
 * 
 * @package PHPAccessControl
 *
 */
abstract class ValueBoundSpecification extends \PHPAccessControl\Specification\LeafSpecification
{
	/**
	 * The value the comparisons are made against.
	 * 
	 * @var int
	 */
	protected $value;

	/**
	 * @param int $value
	 */
	public function __construct($value)
	{
		$this->value = (int) $value;
	}
}