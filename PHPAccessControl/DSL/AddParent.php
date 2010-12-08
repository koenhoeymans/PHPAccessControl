<?php

namespace PHPAccessControl\DSL;

use PHPAccessControl\Specification\InheritanceList;
use PHPAccessControl\Specification\Specification;

/**
 * Utility class to provide higher level language to add a parent to
 * an aco.
 * 
 * @package PHPAccessControl
 */
class AddParent
{
	/**
	 * The parent aco
	 * 
	 * @var Specification
	 */
	private $parent;

	/**
	 * The InheritanceStore the relationship is added to.
	 * 
	 * @var InheritanceStore
	 */
	private $store;

	/**
	 * @param Specification $parent
	 * @param InheritanceList $store
	 */
	public function __construct(Specification $parent, InheritanceList $store)
	{
		$this->parent = $parent;
		$this->store = $store;
	}

	/**
	 * To child aco in the parent relationship (the aco that inherits
	 * permissions from the parent aco).
	 * 
	 * @param Specification $child
	 */
	public function to(Specification $child)
	{
		$this->store->addParent($this->parent, $child);
		return $this;
	}
}