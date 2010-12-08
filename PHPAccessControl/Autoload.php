<?php

/**
 * @package PHPAccessControl
 */

/**
 * Loads the files for the PHPAccessControl library.
 */
function PHPAccessControl_Autoload($className)
{
	$classNameFile = __DIR__
		. DIRECTORY_SEPARATOR . '..'
		. DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className)
		. '.php';

	if (file_exists($classNameFile))
	{
		require_once $classNameFile;
	}
}

spl_autoload_register('PHPAccessControl_Autoload');