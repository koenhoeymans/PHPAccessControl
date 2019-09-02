<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation;

class PHPAccessControl_Situation_SituationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function isSpecialCaseOfSituationIfSubjectAndObjectAreSpecialCasesAndTheActionIsTheSame()
	{
		$this->assertTrue(
			Situation::UserViewPostWithCategoryIdEquals5()->isSpecialCaseOf(
				Situation::UserViewPost()
			)
		);
		$this->assertFalse(
			Situation::UserViewPost()->isSpecialCaseOf(
				Situation::UserViewPostWithCategoryIdEquals5()
			)
		);
	}
}