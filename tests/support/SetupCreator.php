<?php

namespace PHPAccessControl;

class SetupCreator
{
	public static function create()
	{
		global $argv, $argc;
		$setupSpecified = false;

		if ($argc > 1)
		{
			foreach ($argv as $key => $argument)
			{
				if ($argument === '--setup')
				{
					$setupKey = $key + 1;
					$setupSpecified = true;
					break;
				}
			}
		}

		if ($setupSpecified)
		{
			if (isset($argv[$setupKey]))
			{
				$class = '\\PHPAccessControl\\Setup\\' . $argv[$setupKey];
				return $class::create();
			}
		}

		return InMemorySetup::create();
	}
}
