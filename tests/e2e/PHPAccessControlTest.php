<?php

namespace PHPAccessControl;

class PHPAccessControlTest extends \PHPUnit\Framework\TestCase
{
    public function setup()
    {
        $this->accessControl = SetupCreator::create();
    }

    /**
     * @test
     */
    public function accessIsDeniedByDefault()
    {
        $ac = $this->accessControl;

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        $this->assertFalse($result->isAllowed());
    }

    /**
     * @test
     */
    public function aSubjectCanBeAllowedAnActionOnAnObject()
    {
        $ac = $this->accessControl;

        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function aSubjectCanBeDeniedAnActionOnAnObject()
    {
        $ac = $this->accessControl;

        $ac->deny(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        $this->assertFalse($result->isAllowed());
    }

    /**
     * @test
     */
    public function aSubjectCanBeAllowedAnActionOnAnObjectThatIsSpecified()
    {
        $ac = $this->accessControl;

        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->equals(5))
        );

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->equals(5))
        );
        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function moreSpecificObjectsInheritPermissionOfLessSpecificObjects()
    {
        $ac = $this->accessControl;

        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->lesserThan(5))
        );

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->equals(4))
        );

        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function whenMoreThanOneRuleAppliesTheMostSpecificMatchingRuleWins()
    {
        $ac = $this->accessControl;

        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->lesserThan(5))
        );

        $ac->deny(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->lesserThan(3))
        );

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->equals(2))
        );
        $this->assertFalse($result->isAllowed());

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->equals(4))
        );

        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function allowWinsFromDenyIfBothAreEquallySpecific()
    {
        $ac = $this->accessControl;

        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->lesserThan(5))
        );

        $ac->deny(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->greaterThan(3))
        );

        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->equals(4))
        );

        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function accessRightsCanBeConditional()
    {
        // given
        $ac = $this->accessControl;

        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->lesserThan(5))
        );

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        // then
        $this->assertTrue($result->isConditional());
    }

    /**
     * @test
     */
    public function whenSituationIsAllowedAndMoreSpecificOneDeniedAccessIsConditional()
    {
        $ac = $this->accessControl;

        // given
        $ac->allow(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        $ac->deny(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')->with($ac->property('postcount')->lesserThan(5))
        );

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('guest'),
            $ac->to('view'),
            $ac->a('post')
        );

        // then
        $conditions = $result->getAllowed();
        $this->assertEquals(
            $conditions,
            $ac->not($ac->a('post')->with($ac->property('postcount')->lesserThan(5)))
        );
    }

    /**
     * @test
     */
    public function roleBasedUsageAllowsMultipleRolesInAccessQuery()
    {
        $ac = $this->accessControl;

        // given
        $ac->allow(
            $ac->a('admin'),
            $ac->to('delete'),
            $ac->a('post')
        );

        $ac->deny(
            $ac->a('user'),
            $ac->to('delete'),
            $ac->a('post')
        );

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('admin')->lAnd($ac->a('user')),
            $ac->to('delete'),
            $ac->a('post')
        );

        // then
        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function everySubjectInheritsFromTheSpecialAnyAco()
    {
        $ac = $this->accessControl;

        // given
        $ac->allow(
            $ac->any(),
            $ac->to('delete'),
            $ac->a('post')
        );

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('admin'),
            $ac->to('delete'),
            $ac->a('post')
        );

        // then
        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function everyObjectInheritsFromTheSpecialAnyAco()
    {
        $ac = $this->accessControl;

        // given
        $ac->allow(
            $ac->a('admin'),
            $ac->to('delete'),
            $ac->any()
        );

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('admin'),
            $ac->to('delete'),
            $ac->a('post')
        );

        // then
        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function everyActionInheritsFromTheSpecialAnyAction()
    {
        $ac = $this->accessControl;

        // given
        $ac->allow(
            $ac->a('admin'),
            $ac->anyAction(),
            $ac->a('post')
        );

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('admin'),
            $ac->to('delete'),
            $ac->a('post')
        );

        // then
        $this->assertTrue($result->isAllowed());
    }

    /**
     * @test
     */
    public function acoInheritanceCanBeManuallyAdded()
    {
        $ac = $this->accessControl;

        // given
        $ac->allow(
            $ac->a('admin'),
            $ac->to('edit'),
            $ac->a('catZ')
        );

        $ac->addParentAco($ac->a('catX'))->to($ac->a('catY'));
        $ac->addParentAco($ac->a('catZ'))->to($ac->a('catX'));

        // when
        $result = $ac->checkPermissionOf(
            $ac->a('admin'),
            $ac->to('edit'),
            $ac->a('catY')
        );

        // then
        $this->assertTrue($result->isAllowed());
    }
}
