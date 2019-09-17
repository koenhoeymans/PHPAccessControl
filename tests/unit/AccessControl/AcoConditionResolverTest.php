<?php

namespace PHPAccessControl;

use PHPAccessControl\AccessControl\AcoConditionResolver;

class AcoConditionResolverTest extends \PHPUnit\Framework\TestCase
{
    public function setup()
    {
        $this->dsl = new SupportDsl();
        $this->permissionResolver = $this->createMock(
            '\\PHPAccessControl\\AccessControl\\PermissionResolver'
        );
        $this->situationStore = $this->createMock(
            '\\PHPAccessControl\\Situation\\SituationStore'
        );
        $this->conditionResolver =
            new AcoConditionResolver($this->permissionResolver, $this->situationStore);
    }

    /**
     * @test
     */
    public function noAccessConditionsWhenRulesDontContainAcosThatAreFurtherSpecified()
    {
        $dsl = $this->dsl;

        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );

        $this->permissionResolver
                ->expects($this->once())
                ->method('isAllowedByInheritance')
                ->will($this->returnValue(true));
        $this->situationStore
                ->expects($this->once())
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $this->assertNull($this->conditionResolver->buildAccessConditionsFor($situation));
    }

    /**
     * @test
     */
    public function accessIsConditionalForSituationWithoutRuleWhenWithFurtherSpecifiedSituationExistsInAllowingRule()
    {
        $dsl = $this->dsl;

        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(500))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(null));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(true));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($situation);

        $this->assertEquals(
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(500)),
            $result
        );
    }

    /**
     * @test
     */
    public function accessIsNotConditionalForAllowedSituationAndAllowedSpecifyingSituation()
    {
        $dsl = $this->dsl;

        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(500))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(true));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($situation);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function accessIsNotConditionalForDeniedSituationAndDeniedSpecifyingSituation()
    {
        $dsl = $this->dsl;

        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(500))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(false));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($situation);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function accessIsConditionalForSituationAllowedButChildSituationDenied()
    {
        $dsl = $this->dsl;

        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(500))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(false));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($situation);

        $notPostWithWordcountLt500 = $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(500))->not();
        $this->assertEquals($notPostWithWordcountLt500, $result);
    }

    /**
     * @test
     */
    public function conditionalAccessForAllowedDeniedAllowed()
    {
        $dsl = $this->dsl;

        $parentSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($parentSituation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(2))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(true));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($situation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(2))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($parentSituation);
        $this->assertEquals(
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))->not()
                ->lOr(
                    $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
                ),
            $result
        );
    }

    /**
     * @test
     */
    public function conditionalAccessForDeniedAllowedDenied()
    {
        $dsl = $this->dsl;

        $parentSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($parentSituation))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(2))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(false));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($situation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(2))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($parentSituation);
        $this->assertEquals(
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
                ->lAnd(
                    $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
                    ->not()
                ),
            $result
        );
    }

    /**
     * @test
     */
    public function conditionalAccessForAllowedAllowedDenied()
    {
        $dsl = $this->dsl;

        $parentSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($parentSituation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(2))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(false));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($situation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(2))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($parentSituation);
        $this->assertEquals(
            $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
                    ->not(),
            $result
        );
    }

    /**
     * @test
     */
    public function conditionalAccessForDeniedDeniedAllowed()
    {
        $dsl = $this->dsl;

        $parentSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $situation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
        );
        $childSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($parentSituation))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($situation))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(2))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituation))
                ->will($this->returnValue(true));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($situation)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituation)));
        $this->situationStore
                ->expects($this->at(2))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($parentSituation);
        $this->assertEquals(
            $dsl->aco('post')
                    ->with($dsl->property('category')->equals('x'))
                    ->with($dsl->property('wordcount')->lesserThan(100)),
            $result
        );
    }

    /**
     * @test
     */
    public function conditionalAccessForDeniedAndTwoChildSituationsAllowed()
    {
        $dsl = $this->dsl;

        $parentSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $childSituationA = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
        );
        $childSituationB = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(100))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($parentSituation))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituationA))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(2))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituationB))
                ->will($this->returnValue(true));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituationA, $childSituationB)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array()));
        $this->situationStore
                ->expects($this->at(2))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($parentSituation);

        $this->assertEquals(
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
            ->lOr($dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(100))),
            $result
        );
    }

/**
     * @test
     */
    public function conditionalAccessForAllowedAndTwoChildSituationsDenied()
    {
        $dsl = $this->dsl;

        $parentSituation = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')
        );
        $childSituationA = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))
        );
        $childSituationB = $dsl->situation(
            $dsl->aco('user'),
            $dsl->action('view'),
            $dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(100))
        );

        $this->permissionResolver
                ->expects($this->at(0))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($parentSituation))
                ->will($this->returnValue(true));
        $this->permissionResolver
                ->expects($this->at(1))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituationA))
                ->will($this->returnValue(false));
        $this->permissionResolver
                ->expects($this->at(2))
                ->method('isAllowedByInheritance')
                ->with($this->equalTo($childSituationB))
                ->will($this->returnValue(false));
        $this->situationStore
                ->expects($this->at(0))
                ->method('getChildren')
                ->will($this->returnValue(array($childSituationA, $childSituationB)));
        $this->situationStore
                ->expects($this->at(1))
                ->method('getChildren')
                ->will($this->returnValue(array()));
        $this->situationStore
                ->expects($this->at(2))
                ->method('getChildren')
                ->will($this->returnValue(array()));

        $result = $this->conditionResolver->buildAccessConditionsFor($parentSituation);

        $this->assertEquals(
            $dsl->aco('post')->with($dsl->property('category')->equals('x'))->not()
            ->lAnd($dsl->aco('post')->with($dsl->property('wordcount')->lesserThan(100))->not()),
            $result
        );
    }
}
