<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'SituationClasses.php';

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'RuleClasses.php';

class PHPAccessControl_Situation_InMemorySituationStoreTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->store = new \PHPAccessControl\Situation\InMemorySituationStore();
	}

	/**
	 * @test
	 */
	public function addSituation()
	{
		$this->store->add(UserViewPost::create());
		foreach ($this->store as $situation)
		{
			if ($situation != UserViewPost::create()) $this->fail();
		}
	}

	/**
	 * @test
	 */
	public function listensForNewlyAddedRulesToAddSituations()
	{
		$this->store->notifyRuleAdded(CreateRule::allow(UserViewPost::create()));
		foreach ($this->store as $situation)
		{
			if ($situation != UserViewPost::create()) $this->fail();
		}
	}
}