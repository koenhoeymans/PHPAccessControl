<?php

/**
 * @package PHPAccessControl
 */

namespace PHPAccessControl\Action;

/**
 * Describes an action.
 * 
 * @package PHPAccessControl
 */
class Action extends \PHPAccessControl\Specification\LeafSpecification
{
	/**
	 * Used to represent any action.
	 * 
	 * @var string
	 */
	const ANY_ACTION = 'any action';

	/**
	 * The name of the action.
	 * 
	 * @var string
	 */
	private $action;

	/**
	 * Constructs the description of action. If no name is given it defaults
	 * to any action.
	 * 
	 * @param string $action
	 */
	public function __construct($action = self::ANY_ACTION)
	{
		$this->action = (string) $action;
	}

	/**
	 * @see PHPAccessControl\Specification.GenericSpecification::isSatisfiedBy()
	 */
	public function isSatisfiedBy($candidate)
	{
		return $candidate === $this->action;
	}

	/**
	 * Is this action a special case of another action?
	 * 
	 * @param Action $action
	 * @return boolean
	 */
	protected function isSpecialCaseOfAction(Action $action)
	{
		if ($action->action === self::ANY_ACTION)
		{
			return true;
		}
		return $action->action === $this->action;
	}

	/**
	 * Is this action more general than another action?
	 * 
	 * @param Action $action
	 * @return boolean
	 */
	protected function isGeneralizationOfAction(Action $action)
	{
		return $action->isSpecialCaseOf($this);
	}
}