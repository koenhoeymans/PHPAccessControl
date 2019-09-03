<?php

namespace PHPAccessControl\Situation;

use PHPAccessControl\TestSituation;

class SituationTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @test
	 */
	public function isSpecialCaseOfSituationIfSubjectAndObjectAreSpecialCasesAndTheActionIsTheSame()
	{
		$this->assertTrue(
			TestSituation::UserViewPostWithCategoryIdEquals5()->isSpecialCaseOf(
				TestSituation::UserViewPost()
			)
		);
		$this->assertFalse(
			TestSituation::UserViewPost()->isSpecialCaseOf(
				TestSituation::UserViewPostWithCategoryIdEquals5()
			)
		);
	}
}
