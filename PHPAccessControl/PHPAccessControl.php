<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl;

use PHPAccessControl\Specification\Specification;
use	PHPAccessControl\Property\Property;
use	PHPAccessControl\Action\Action;
use	PHPAccessControl\Action\AnyAction;
use PHPAccessControl\AccessControledObject\Aco;
use	PHPAccessControl\AccessControl\PermissionResolver;
use	PHPAccessControl\AccessControl\PermissionList;
use PHPAccessControl\Specification\InheritanceList;

/**
 * This iss the facade to the access control system.
 * 
 * @package PHPAccessControl
 */
class PHPAccessControl
{
	/**
	 * @var PermissionResolver
	 */
	private $permissionResolver;

	/**
	 * @var PermissionList
	 */
	private $permissionList;

	/**
	 * @var InheritanceList
	 */
	private $acoInheritanceList;

	/** 
	 * @param	PermissionResolver $permissionResolver
	 * @param	PermissionList $permissionList
	 * @param	InheritanceList $specificationInheritanceList
	 */
	public function __construct(
		PermissionResolver $permissionResolver, PermissionList $permissionList,
		InheritanceList $specificationInheritanceList
	) {
		$this->permissionResolver = $permissionResolver;
		$this->permissionList = $permissionList;
		$this->specificationInheritanceList = $specificationInheritanceList;
	}

	/**
	 * Checks permission of a subject to do an action on an object.
	 * 
	 * @param	Specification $subject
	 * @param	Action $action
	 * @param	Specification $object
	 * @return	Result\Result
	 */
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
		$allowed = $this->permissionResolver->isAllowed($specification);
		$conditions = $this->permissionResolver->buildAccessConditionsFor($specification);
		return new Result\Result($allowed, $conditions);
	}

	/**
	 * Returns the negation of the specification.
	 * 
	 * @param	Specification $specification
	 * @return	Specification
	 */
	public function not(Specification $specification)
	{
		return $specification->not();
	}

	/**
	 * Creates an unspecified access controled object ('any aco').
	 * 
	 * @return Aco
	 */
	public function any()
	{
		return new Aco();
	}

	/**
	 * Create an access controled object with a given name.
	 * 
	 * @param	string $name
	 * @return	Aco
	 */
	public function a($name)
	{
		return new Aco($name);
	}

	/**
	 * Creates an action.
	 * 
	 * @param	string $action
	 * @return	Action
	 */
	public function to($action)
	{
		return new Action($action);
	}

	/**
	 * Creates an unspecified action ('any action').
	 * 
	 * @return	Action
	 */
	public function anyAction()
	{
		return new Action();
	}

	/**
	 * Adds an access controled object (aco) as parent to another aco
	 * so permissions are inherited from the parent aco.
	 * 
	 * @param	Specification $parent
	 * @return	DSL\AddParent
	 */
	public function addParentAco(Specification $parent)
	{
		return new DSL\AddParent($parent, $this->specificationInheritanceList);
	}

	/**
	 * Creates a property with a given name.
	 * 
	 * @param	string $name
	 * @return	\PHPAccessControl\Property\PropertyDSL DSL for specifying properties
	 */
	public function property($name)
	{
		return new \PHPAccessControl\Property\PropertyDSL($name);
	}

	/**
	 * Allows a subject an action on an object.
	 * 
	 * @param	Specification $subject
	 * @param	Action $action
	 * @param	Specification $object
	 */
	public function allow(
		Specification $subject, Action $action,	Specification $object
	) {
		$this->addRule(true, $subject, $action, $object);
	}

	/**
	 * Denies a subject an action on an object.
	 * 
	 * @param	Specification $subject
	 * @param	Action $action
	 * @param	Specification $object
	 */
	public function deny(
		Specification $subject, Action $action,	Specification $object
	) {
		$this->addRule(false, $subject, $action, $object);
	}

	/**
	 * Adds the permission of a situation to the permissionlist.
	 * 
	 * @param	bool $allowed
	 * @param	Specification $subject
	 * @param	Action $action
	 * @param	Specification $object
	 */
	private function addRule(
		$allowed, Specification $subject, Action $action, Specification $object
	) {
		$situation = $this->createSituation($subject, $action, $object);
		$method = $allowed ? 'allow' : 'deny';
		$this->permissionList->$method($situation);
	}

	/**
	 * Creates a situation from a given subject/action/object combination.
	 * 
	 * @param	Specification $subject
	 * @param	Action $action
	 * @param	Specification $object
	 */
	private function createSituation(
		Specification $subject, Action $action, Specification $object
	) {
		return new Situation\Situation($subject, $action, $object);
	}
}