<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'SituationClasses.php';

class PHPAccessControl_Situation_SituationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function isSpecialCaseOfSituationIfSubjectAndObjectAreSpecialCasesAndTheActionIsTheSame()
	{
		$this->assertTrue(UserViewPost::withCategoryIdEquals5()->isSpecialCaseOf(UserViewPost::create()));
		$this->assertFalse(UserViewPost::create()->isSpecialCaseOf(UserViewPost::withCategoryIdEquals5()));
	}
}