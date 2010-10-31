<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..' 
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\Property\PropertyDSL;
use PHPAccessControl\Property\Property;
use PHPAccessControl\Specification\LogicalAnd;
use PHPAccessControl\Specification\ValueBoundSpecification\Equals;
use PHPAccessControl\Specification\ValueBoundSpecification\LesserThan;
use PHPAccessControl\Specification\ValueBoundSpecification\GreaterThan;

class PHPAccessControl_Property_PropertyDSLTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function equalsAddsEqualSpecification()
	{
		$DSLPostcountEquals5 = PropertyDSL::named('postcount')->equals(5);
		$postcountEquals5 = new Property('postcount', new Equals(5));
		$this->assertTrue($DSLPostcountEquals5->isEqualTo($postcountEquals5));
	}

	/**
	 * @test
	 */
	public function lesserThanAddsLesserThanSpecification()
	{
		$DSLPostcountLesserThan5 = PropertyDSL::named('postcount')->lesserThan(5);
		$postcountLesserThan5 = new Property('postcount', new LesserThan(5));
		$this->assertTrue($DSLPostcountLesserThan5->isEqualTo($postcountLesserThan5));
	}

	/**
	 * @test
	 */
	public function greaterThanAddsGreaterThanSpecification()
	{
		$DSLPostcountGreaterThan5 = PropertyDSL::named('postcount')->greaterThan(5);
		$postcountGreaterThan5 = new Property('postcount', new GreaterThan(5));
		$this->assertTrue($DSLPostcountGreaterThan5->isEqualTo($postcountGreaterThan5));
	}

	/**
	 * @test
	 */
	public function DSLCanBeUsedMultipleTimesToCombineSpecifications()
	{
		$DSLPostcountLesserThan5AndGreaterThan2 = PropertyDSL::named('postcount')->lesserThan(5)->greaterThan(2);
		$postcountLesserThan5AndGreaterThan2 = new Property(
			'postcount',
			new LogicalAnd(new LesserThan(5), new GreaterThan(2))
		);
		$this->assertTrue($DSLPostcountLesserThan5AndGreaterThan2->isEqualTo($postcountLesserThan5AndGreaterThan2));
	}
}