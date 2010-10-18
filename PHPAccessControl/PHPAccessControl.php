<?php

namespace PHPAccessControl;

use PHPAccessControl\Specification\Specification;
use	PHPAccessControl\Property\Property;
use	PHPAccessControl\Action\Action;
use	PHPAccessControl\Action\AnyAction;
use	PHPAccessControl\AccessControl\PermissionResolver;
use	PHPAccessControl\AccessControl\ConditionResolver;
use	PHPAccessControl\Rule\RuleList;
use PHPAccessControl\Specification\InheritanceList;

class PHPAccessControl
{
	private $permissionResolver;

	private $conditionResolver;

	private $ruleList;

	private $acoInheritanceList;

	public function __construct(
		PermissionResolver $permissionResolver, ConditionResolver $conditionResolver,
		RuleList $ruleList, InheritanceList $specificationInheritanceList
	) {
		$this->permissionResolver = $permissionResolver;
		$this->conditionResolver = $conditionResolver;
		$this->ruleList = $ruleList;
		$this->specificationInheritanceList = $specificationInheritanceList;
	}

	public function checkPermissionOf(
		Specification $subject, Action $action, Specification $object
	) {
		$subjectParents = $this->specificationInheritanceList->getParentsRecursively($subject);
		foreach($subjectParents as $parent)
		{
			$subject = $subject->lAnd($parent);
		}
		$objectParents = $this->specificationInheritanceList->getParentsRecursively($object);
		foreach($objectParents as $parent)
		{
			$object = $object->lAnd($parent);
		}
		$specification = $this->createSituation($subject, $action, $object);
		$allowed = $this->permissionResolver->isAllowedByInheritance($specification);
		$conditions = $this->conditionResolver->buildAccessConditionsFor($specification);
		return new Result\Result($allowed, $conditions);
	}

	public function not(Specification $specification)
	{
		return $specification->not();
	}

	public function any()
	{
		return new \PHPAccessControl\AccessControledObject\Aco();
	}

	public function a($name)
	{
		return new AccessControledObject\Aco($name);
	}

	public function to($action)
	{
		return new Action($action);
	}

	public function anyAction()
	{
		return new Action();
	}

	public function addParentAco(Specification $parent)
	{
		return new DSL\AddParent($parent, $this->specificationInheritanceList);
	}

	/**
	 * @todo move to DSL
	 */
	public function property($name)
	{
		return new \PHPAccessControl\Property\PropertyDSL($name);
	}

	public function allow(
		Specification $subject, Action $action,	Specification $object
	) {
		$this->addRule(true, $subject, $action, $object);
	}

	public function deny(
		Specification $subject, Action $action,	Specification $object
	) {
		$this->addRule(false, $subject, $action, $object);
	}

	private function addRule(
		$allowed, Specification $subject, Action $action, Specification $object
	) {
		$situation = $this->createSituation($subject, $action, $object);
		$this->ruleList->addRule(new Rule\SituationBasedRule($situation, $allowed));
	}

	private function createSituation(
		Specification $subject, Action $action, Specification $object
	) {
		return new Situation\Situation($subject, $action, $object);
	}
}