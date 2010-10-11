<?php

class Dsl
{
	public function situation(
		\PHPAccessControl\AccessControledObject\Aco $subject,
		\PHPAccessControl\Action\Action $action,
		\PHPAccessControl\AccessControledObject\Aco $object
	) {
		return new \PHPAccessControl\Situation\Situation($subject, $action, $object);
	}

	public function action($name)
	{
		return new \PHPAccessControl\Action\Action($name);	
	}

	public function aco($name)
	{
		return new \PHPAccessControl\AccessControledObject\Aco($name);
	}

	public function property($name)
	{
		return new \PHPAccessControl\Property\PropertyDSL($name);
	}
}