<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Situation;

use PHPAccessControl\Action\Action;
use	PHPAccessControl\Specification\Specification;

/**
 * A situation is what is allowed or denied to happen. It consists of
 * an object that does an action on another object. The objects can be
 * anything (role, group, user, ...).
 * 
 * @package PHPAccessControl
 */
class Situation extends \PHPAccessControl\Specification\LeafSpecification
{
	/**
	 * The object that does the action.
	 * 
	 * @var Specification
	 */
	private $subject;

	/**
	 * The action.
	 * 
	 * @var Action
	 */
	private $action;

	/**
	 * The object that undergoes the action.
	 * 
	 * @var Specification
	 */
	private $object;

	/**
	 * Constructs a situation consisting of a subject that does an action
	 * on an object.
	 * 
	 * @param Specification $subject
	 * @param Action $action
	 * @param Specification $object
	 */
	public function __construct(
		Specification $subject, Action $action, Specification $object
	) {
		$this->subject = $subject;
		$this->action = $action;
		$this->object = $object;
	}

	/**
	 * Return the object.
	 * 
	 * @return Specification
	 */
	public function getAco()
	{
		return $this->object;
	}

	/**
	 * Is this situation a special case of another situation?
	 * 
	 * @param Situation $situation
	 * @return boolean
	 */
	protected function isSpecialCaseOfSituation(Situation $situation)
	{
		$subjectSpecialCase = $this->subject->isSpecialCaseOf($situation->subject);
		$actionSame = $this->action->isSpecialCaseOf($situation->action);
		$objectSpecialCase = $this->object->isSpecialCaseOf($situation->object);
		return $subjectSpecialCase && $actionSame && $objectSpecialCase;
	}

	/**
	 * Is this situation a special case of another situation?
	 * 
	 * @param Situation $situation
	 * @return boolean
	 */
	protected function isGeneralizationOfSituation(Situation $situation)
	{
		return $situation->isSpecialCaseOf($this);
	}
}