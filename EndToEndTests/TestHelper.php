<?php

error_reporting(-1);

$path = array(
	dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $path));

function __autoload($className)
{
	$classNameFile = dirname(__FILE__)
		. DIRECTORY_SEPARATOR . '..'
		. DIRECTORY_SEPARATOR
		. str_replace('\\', DIRECTORY_SEPARATOR, $className)
		. '.php';
	if (file_exists($classNameFile))
	{
		require_once($classNameFile);
	}
}

?>