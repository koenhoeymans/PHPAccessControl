<?php

namespace PHPAccessControl\Situation;

use PHPAccessControl\TestSituation;
use PHPAccessControl\CreateRule;

class InMemorySituationStoreTest extends \PHPUnit\Framework\TestCase
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
        $this->store->add(TestSituation::UserViewPost());
        foreach ($this->store as $situation) {
            if ($situation != TestSituation::UserViewPost()) {
                $this->fail();
            }
        }
    }

    /**
     * @test
     */
    public function listensForNewlyAddedRulesToAddSituations()
    {
        $this->store->notifyRuleAdded(CreateRule::allow(TestSituation::UserViewPost()));
        foreach ($this->store as $situation) {
            if ($situation != TestSituation::UserViewPost()) {
                $this->fail();
            }
        }
    }
}
