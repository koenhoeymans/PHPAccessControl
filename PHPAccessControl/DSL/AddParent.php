<?php

namespace PHPAccessControl\DSL;

use PHPAccessControl\Specification\InheritanceList;
use PHPAccessControl\Specification\Specification;

class AddParent
{
	private $parent;

	private $store;

	private $accessControl;

	public function __construct(Specification $parent, InheritanceList $store)
	{
		$this->parent = $parent;
		$this->store = $store;
	}

	public function to(Specification $child)
	{
		$this->store->addParent($this->parent, $child);
		return $this;
	}
}