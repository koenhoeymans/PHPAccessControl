<?php

namespace PHPAccessControl\Rule;

class InMemoryRuleList implements RuleList
{
	const RULE_ADDED = 'RuleAdded';

	private $observers = array();

	private $rules = array();

	public function addObserver(RuleListObserver $observer)
	{
		$this->observers[] = $observer;
	}

	private function notify($event, $data)
	{
		foreach ($this->observers as $observer)
		{
			$method = 'notify' . $event;
			$observer->$method($data);
		}
	}

	public function addRule(SituationBasedRule $rule)
	{
		if (!in_array($rule, $this->rules))
		{
			$this->rules[] = $rule;
			$this->notify(self::RULE_ADDED, $rule);
		}
	}

	public function getAllRules()
	{
		return $this->rules;
	}
}