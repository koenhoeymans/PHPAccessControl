<?php

namespace PHPAccessControl\Action;

class Action extends \PHPAccessControl\Specification\LeafSpecification
{
	const ANY_ACTION = 'any action';

	private $action;

	public function __construct($action = self::ANY_ACTION)
	{
		$this->action = (string) $action;
	}

	public function isSatisfiedBy($candidate)
	{
		return $candidate === $this->action;
	}

	public function isSpecialCaseOfAction(Action $action)
	{
		if ($action->action === self::ANY_ACTION)
		{
			return true;
		}
		return $action->action === $this->action;
	}

	public function isGeneralizationOfAction(Action $action)
	{
		return $action->isSpecialCaseOf($this);
	}
}