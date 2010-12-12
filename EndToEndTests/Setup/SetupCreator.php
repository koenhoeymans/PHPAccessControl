<?php

namespace PHPAccessControl\EndToEndTests\Setup;

/**
 * Creates a setup for the end-to-end tests.
 */
class SetupCreator
{
	/**
	 * Looks for the '--setup' argument after 'phpunit'. If none is found it returns
	 * 'InMemorySetup' as the default setup.
	 * 
	 * @return \PHPAccessControl\PHPAccessControl
	 */
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
				$class = '\\PHPAccessControl\\EndToEndTests\\Setup\\' . $argv[$setupKey];
				return $class::create();
			}
		}

		return InMemorySetup::create();
	}
}