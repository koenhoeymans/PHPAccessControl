<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PropertyClasses.php';

class CreateAco
{
	static public function name($name)
	{
		return new \PHPAccessControl\AccessControledObject\Aco($name);
	}

	static public function postWithCategoryIdEquals5()
	{
		return 	CreateAco::name('post')->with(CategoryId::equals5());
	}

	static public function postWithCategoryIdEquals5AndWordCountGreaterThan100()
	{
		return CreateAco::name('post')
				->with(CategoryId::equals5())
				->with(Wordcount::greaterThan(100));
	}

	static public function postWithWordCountGreaterThan($x)
	{
		return CreateAco::name('post')
				->with(Wordcount::greaterThan($x));
	}
}