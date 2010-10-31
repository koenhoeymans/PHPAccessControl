<?php

namespace PHPAccessControl\UnitTests\Support;

class CustomAssertions
{
	public function arraysHaveEqualElements(array $array1, array $array2)
	{
		foreach ($array1 as $element1)
		{
			$key = array_search($element1, $array2);
			if ($key === false)
			{
				return false;
			}
			unset($array2[$key]);
		}
		return empty($array2);
	}
}