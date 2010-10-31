<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..' 
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\Property\Property;
use PHPAccessControl\Specification\ValueBoundSpecification\LesserThan;

class PHPAccessControl_Property_PropertyTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function staticMethodCreatesInstanceProvidingDSL()
	{
		$instanceA = Property::named('a');
		$instanceB = new Property('a');
		$this->assertEquals($instanceA, $instanceB);
	}

	/**
	 * @test
	 */
	public function canDetermineWhetherItIsSpecialCaseOfOtherProperty()
	{
		$age = Property::named('age');
		$postcount = Property::named('postcount');
		$postcountLesserThan4 = new Property('postcount', new LesserThan(4));
		$postcountLesserThan5 = new Property('postcount', new LesserThan(5));
		$this->assertFalse($age->isSpecialCaseOf($postcount));
		$this->assertTrue($age->isSpecialCaseOf($age));
		$this->assertFalse($postcount->isSpecialCaseOf($postcountLesserThan5));
		$this->assertTrue($postcountLesserThan4->isSpecialCaseOf($postcountLesserThan5));
		$this->assertFalse($postcountLesserThan5->isSpecialCaseOf($postcountLesserThan4));
		$this->assertTrue($postcountLesserThan5->isSpecialCaseOf($postcount));
	}

	/**
	 * @test
	 */
	public function canDetermineWhetherItIsGeneralizationOfOtherProperty()
	{
		$age = Property::named('age');
		$postcount = Property::named('postcount');
		$postcountLesserThan4 = new Property('postcount', new LesserThan(4));
		$postcountLesserThan5 = new Property('postcount', new LesserThan(5));
		$this->assertFalse($age->isGeneralizationOf($postcount));
		$this->assertTrue($age->isGeneralizationOf($age));
		$this->assertTrue($postcount->isGeneralizationOf($postcountLesserThan5));
		$this->assertFalse($postcountLesserThan4->isGeneralizationOf($postcountLesserThan5));
		$this->assertTrue($postcountLesserThan5->isGeneralizationOf($postcountLesserThan4));
		$this->assertFalse($postcountLesserThan5->isGeneralizationOf($postcount));
	}
}