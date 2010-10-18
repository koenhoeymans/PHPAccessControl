<?php

namespace PHPAccessControl\UnitTests\Support;

use PHPAccessControl\AccessControledObject\Aco;
use PHPAccessControl\Property\PropertyDSL as aProperty;
use PHPAccessControl\Action\Action;

class Situation
{
	public static function adminEditContent()
	{
		return new \PHPAccessControl\Situation\Situation(
			Aco::named('admin'), new Action('edit'), Aco::named('content')
		);
	}

	public static function userViewPost()
	{
		return new \PHPAccessControl\Situation\Situation(
			Aco::named('user'),	new Action('view'),	Aco::named('post')
		);
	}

	public static function userViewPostWithCategoryIdEquals5()
	{
		return new \PHPAccessControl\Situation\Situation(
			Aco::named('user'),
			new Action('view'),
			Aco::named('post')->with(aProperty::named('categoryId')->equals(5))
		);
	}

	public static function userViewPostWithWordCountGreaterThan100()
	{
		return new \PHPAccessControl\Situation\Situation(
			Aco::named('user'),
			new Action('view'),
			Aco::named('post')->with(aProperty::named('wordcount')->greaterThan(100))
		);
	}

	public static function userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100()
	{
		return new \PHPAccessControl\Situation\Situation(
			Aco::named('user'),
			new Action('view'),
			Aco::named('post')->with(
				aProperty::named('categoryId')->equals(5)->lAnd(aProperty::named('wordcount')->greaterThan(100))
			)
		);
	}
}