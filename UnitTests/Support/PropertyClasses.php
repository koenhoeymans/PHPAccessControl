<?php

class CategoryId
{
	public static function equals5()
	{
		$categoryId = new \PHPAccessControl\Property\PropertyDSL('category id');
		$categoryIdEquals5 = $categoryId->equals(5);
		return $categoryIdEquals5;
	}
}

class Wordcount
{
	public static function greaterThan($x)
	{
		$wordcount = new \PHPAccessControl\Property\PropertyDSL('wordcount');
		$wordcountGreaterThanX = $wordcount->greaterThan($x);
		return $wordcountGreaterThanX;
	}
}