<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation as a;

class PHPAccessControl_Situation_SituationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function isSpecialCaseOfSituationIfSubjectActionObjectAreTheSame()
	{
		$this->assertTrue(
			a::UserViewPostWithCategoryIdEquals5()->isSpecialCaseOf(
				a::UserViewPostWithCategoryIdEquals5()
			)
		);
	}

	/**
	 * @test
	 */
	public function isSpecialCaseOfSituationIfSubjectActionObjectAreAllSpecialCases()
	{
		$this->assertTrue(
			a::UserViewPostWithCategoryIdEquals5()->isSpecialCaseOf(
				a::UserViewPost()
			)
		);
		$this->assertFalse(
			a::UserViewPost()->isSpecialCaseOf(
				a::UserViewPostWithCategoryIdEquals5()
			)
		);
	}
	

	/**
	 * @test
	 */
	public function isGeneralizationOfSituationIfSubjectActionObjectAreSpecialCases()
	{
		$this->assertFalse(
			a::UserViewPostWithCategoryIdEquals5()->isGeneralizationOf(
				a::UserViewPost()
			)
		);
		$this->assertTrue(
			a::UserViewPost()->isGeneralizationOf(
				a::UserViewPostWithCategoryIdEquals5()
			)
		);
	}
}