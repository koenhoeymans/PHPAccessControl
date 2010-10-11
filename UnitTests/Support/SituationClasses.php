<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AcoClasses.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PropertyClasses.php';

class AdminEditContent
{
	static public function create()
	{
		return new \PHPAccessControl\Situation\Situation(
			CreateAco::name('admin'),
			new \PHPAccessControl\Action\Action('edit'),
			CreateAco::name('content')
		);
	}
}

class UserViewPost
{
	static public function create()
	{
		return new \PHPAccessControl\Situation\Situation(
			CreateAco::name('user'),
			new \PHPAccessControl\Action\Action('view'),
			CreateAco::name('post')
		);
	}

	static public function withCategoryIdEquals5()
	{
		return new \PHPAccessControl\Situation\Situation(
			CreateAco::name('user'),
			new \PHPAccessControl\Action\Action('view'),
			CreateAco::name('post')->with(CategoryId::equals5())
		);
	}

	static public function withWordCountGreaterThan100()
	{
		return new \PHPAccessControl\Situation\Situation(
			CreateAco::name('user'),
			new \PHPAccessControl\Action\Action('view'),
			CreateAco::name('post')->with(Wordcount::greaterThan(100))
		);
	}

	static public function withPostCategoryIdEquals5AndWordCountGreaterThan100()
	{
		$post = CreateAco::name('post');
		$post = $post->with(CategoryId::equals5());
		$post = $post->with(Wordcount::greaterThan(100));
		return new \PHPAccessControl\Situation\Situation(
			CreateAco::name('user'),
			new \PHPAccessControl\Action\Action('view'),
			$post
		);
	}
}