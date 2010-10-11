<?php

namespace PHPAccessControl\Property;

class PropertyList implements \Iterator
{
	private $properties = array();

	public function rewind()
	{
		reset($this->properties);
	}

	public function current()
	{
		return current($this->properties);
	}

	public function key()
	{
		return key($this->properties);
	}

	public function next()
	{
		next($this->properties);
	}

	public function valid()
	{
		return key($this->properties) !== null;
	}

	public function isEmpty()
	{
		return $this->properties === array();
	}

	public function add(Property $property)
	{
		if (!in_array($property, $this->properties))
		{
			$this->properties[] = $property;
		}
		return $this;
	}
}