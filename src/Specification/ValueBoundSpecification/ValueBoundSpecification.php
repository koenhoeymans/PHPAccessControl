<?php

namespace PHPAccessControl\Specification\ValueBoundSpecification;

abstract class ValueBoundSpecification extends \PHPAccessControl\Specification\LeafSpecification
{
	protected $value;

	public function __construct($value)
	{
		$this->value = $value;
	}
}