<?php

namespace PHPAccessControl\Action;

class ActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function satisfiedByStringName()
    {
        $action = new \PHPAccessControl\Action\Action('view');
        $action->isSatisfiedBy('view');
    }

    /**
     * @test
     */
    public function anyActionIsGeneralizationOfAnAction()
    {
        $anyAction = new \PHPAccessControl\Action\Action('any action');
        $view = new \PHPAccessControl\Action\Action('view');
        $this->assertTrue($anyAction->isGeneralizationOf($view));
        $this->assertFalse($view->isGeneralizationOf($anyAction));
    }

    /**
     * @test
     */
    public function anActionIsSpecialCaseOfAnyAction()
    {
        $anyAction = new \PHPAccessControl\Action\Action('any action');
        $view = new \PHPAccessControl\Action\Action('view');
        $this->assertTrue($view->isSpecialCaseOf($anyAction));
        $this->assertFalse($anyAction->isSpecialCaseOf($view));
    }
}
