<?php

namespace PHPAccessControl;

use PHPAccessControl\TestSituation;
use PHPAccessControl\CreateRule;

class AlgorithmicPermissionResolverTest extends \PHPUnit\Framework\TestCase
{
    public function setup()
    {
        $this->rules = new \PHPAccessControl\Rule\InMemoryRuleList();
        $this->situationStore = new \PHPAccessControl\Situation\InMemorySituationStore();
        $this->ruleFinder = new \PHPAccessControl\Rule\SimpleRuleFinder($this->rules, $this->situationStore);
        $this->permissionResolver =
            new \PHPAccessControl\AccessControl\AlgorithmicPermissionResolver($this->ruleFinder);
    }

    /**
     * @test
     */
    public function specificationIsDeniedWhenThereIsNoMatchingRule()
    {
        $this->assertFalse(
            $this->permissionResolver->isAllowedByInheritance(TestSituation::userViewPost())
        );
    }

    /**
     * @test
     */
    public function isAllowedByInheritanceWhenMatchingAllowingRuleExists()
    {
        $this->rules->addRule(CreateRule::allow(TestSituation::userViewPost()));
        $this->assertTrue(
            $this->permissionResolver->isAllowedByInheritance(TestSituation::userViewPost())
        );
    }

    /**
     * @test
     */
    public function isAllowedByInheritanceWhenMatchingDenyingRuleExists()
    {
        $this->rules->addRule(CreateRule::deny(TestSituation::userViewPost()));
        $this->assertFalse(
            $this->permissionResolver->isAllowedByInheritance(TestSituation::userViewPost())
        );
    }

    /**
     * @test
     */
    public function isAllowedByInheritanceWhenNotAllowedNorDeniedButMoreGeneralSpecificationisAllowedByInheritance()
    {
        $this->rules->addRule(CreateRule::allow(TestSituation::userViewPost()));
        $this->assertTrue(
            $this->permissionResolver->isAllowedByInheritance(TestSituation::userViewPostWithCategoryIdEquals5())
        );
    }

    /**
     * @test
     */
    public function withMultipleLevelsOfAccessRightsTheClosestOneDeterminesInheritedPermission()
    {
        $this->rules->addRule(CreateRule::allow(TestSituation::userViewPost()));
        $this->rules->addRule(CreateRule::deny(TestSituation::userViewPostWithCategoryIdEquals5()));
        $this->assertFalse(
            $this->permissionResolver->isAllowedByInheritance(
                TestSituation::userViewPostWithPostCategoryIdEquals5AndWordCountGreaterThan100()
            )
        );
    }

    /**
     * @test
     */
    public function allowedWinsFromDenied()
    {
        $this->rules->addRule(CreateRule::deny(TestSituation::userViewPost()));
        $this->rules->addRule(CreateRule::allow(TestSituation::userViewPost()));
        $this->assertTrue(
            $this->permissionResolver->isAllowedByInheritance(TestSituation::userViewPost())
        );
    }
}
