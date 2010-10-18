<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';

use PHPAccessControl\UnitTests\Support\Situation;
use PHPAccessControl\UnitTests\Support\CreateRule;

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
		$this->store->add(Situation::UserViewPost());
		foreach ($this->store as $situation)
		{
			if ($situation != Situation::UserViewPost()) $this->fail();
		}
	}

	/**
	 * @test
	 */
	public function listensForNewlyAddedRulesToAddSituations()
	{
		$this->store->notifyRuleAdded(CreateRule::allow(Situation::UserViewPost()));
		foreach ($this->store as $situation)
		{
			if ($situation != Situation::UserViewPost()) $this->fail();
		}
	}
}