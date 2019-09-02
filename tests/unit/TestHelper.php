<?php

error_reporting(-1);

require_once __DIR__
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'PHPAccessControl'
	. DIRECTORY_SEPARATOR . 'Autoload.php';

function PHPAccessControl_UnitTests_Autoload($className)
{
	$classNameFile = __DIR__
		. DIRECTORY_SEPARATOR . '..'
		. DIRECTORY_SEPARATOR . '..'
		. DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className)
		. '.php';

	if (file_exists($classNameFile))
	{
		require_once $classNameFile;
	}
}

spl_autoload_register('PHPAccessControl_UnitTests_Autoload');