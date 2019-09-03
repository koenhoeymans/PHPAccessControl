<?php

namespace PHPAccessControl;

use PHPAccessControl\AccessControledObject\Aco as Aco;
use PHPAccessControl\Action\Action as Action;
use PHPAccessControl\Situation\Situation as Situation;
use PHPAccessControl\Property\PropertyDSL as PropertyDSL;

class SupportDsl
{
    public function situation(Aco $subject, Action $action, Aco $object)
    {
		return new Situation($subject, $action, $object);
	}

	public function action($name)
	{
		return new Action($name);	
	}

	public function aco($name)
	{
		return new Aco($name);
	}

	public function property($name)
	{
		return new PropertyDSL($name);
	}
}
