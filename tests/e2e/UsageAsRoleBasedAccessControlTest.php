<?php

namespace PHPAccessControl;

/**
 * Takes the example in the Zend Framework ACL documentation as a use case.
 * See http://framework.zend.com/manual/en/zend.acl.html (version 1.10)
 */
class UsageAsRoleBasedAccessControl extends \PHPUnit\Framework\TestCase
{
    public function setup()
    {
        $this->accessControl = SetupCreator::create();
    }

    /**
     * @test
     */
    public function usageAsRoleBaseAccessControl()
    {
        $ac = $this->accessControl;

        $ac->addParentAco($ac->a('guest'))->to($ac->a('staff'));
        $ac->addParentAco($ac->a('staff'))->to($ac->a('editor'));

        $ac->allow($ac->a('guest'), $ac->to('view'), $ac->any());
        $ac->allow($ac->a('staff'), $ac->to('edit'), $ac->any());
        $ac->allow($ac->a('staff'), $ac->to('submit'), $ac->any());
        $ac->allow($ac->a('staff'), $ac->to('revise'), $ac->any());
        $ac->allow($ac->a('editor'), $ac->to('publish'), $ac->any());
        $ac->allow($ac->a('editor'), $ac->to('archive'), $ac->any());
        $ac->allow($ac->a('editor'), $ac->to('delete'), $ac->any());
        $ac->allow($ac->a('administrator'), $ac->anyAction(), $ac->any());

        $result = $ac->checkPermissionOf($ac->a('guest'), $ac->to('view'), $ac->any());
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('staff'), $ac->to('publish'), $ac->any());
        $this->assertFalse($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('staff'), $ac->to('revise'), $ac->any());
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('editor'), $ac->to('view'), $ac->any());
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('editor'), $ac->to('update'), $ac->any());
        $this->assertFalse($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('administrator'), $ac->to('view'), $ac->any());
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('administrator'), $ac->anyAction(), $ac->any());
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('administrator'), $ac->to('update'), $ac->any());
        $this->assertTrue($result->isAllowed());

        $ac->addParentAco($ac->a('staff'))->to($ac->a('marketing'));

        $ac->allow($ac->a('marketing'), $ac->to('publish'), $ac->a('newsletter'));
        $ac->allow($ac->a('marketing'), $ac->to('archive'), $ac->a('newsletter'));
        $ac->allow($ac->a('marketing'), $ac->to('publish'), $ac->a('latest'));
        $ac->allow($ac->a('marketing'), $ac->to('archive'), $ac->a('latest'));
        $ac->deny($ac->a('staff'), $ac->to('revise'), $ac->a('latest'));
        $ac->deny($ac->any(), $ac->to('archive'), $ac->a('announcement'));

        $result = $ac->checkPermissionOf($ac->a('staff'), $ac->to('publish'), $ac->a('newsletter'));
        $this->assertFalse($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('marketing'), $ac->to('publish'), $ac->a('newsletter'));
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('staff'), $ac->to('publish'), $ac->a('latest'));
        $this->assertFalse($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('marketing'), $ac->to('publish'), $ac->a('latest'));
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('marketing'), $ac->to('archive'), $ac->a('latest'));
        $this->assertTrue($result->isAllowed());
        $result = $ac->checkPermissionOf($ac->a('marketing'), $ac->to('latest'), $ac->a('revise'));
        $this->assertFalse($result->isAllowed());
        /**
         * note: acces for editor-archive-announcement = contrast with Zend ACL
         * + allowed editor to archive anything
         * - denied any to archive announcement
         * = competing rules here on same level => allow wins from deny
         */
        $result = $ac->checkPermissionOf($ac->a('editor'), $ac->to('archive'), $ac->a('announcement'));
        $this->assertTrue($result->isAllowed());
        /**
         * Same difference here.
         * There's a different lineage that allows this. So allow wins from deny.
         */
        $result = $ac->checkPermissionOf($ac->a('administrator'), $ac->to('announcement'), $ac->a('archive'));
        $this->assertTrue($result->isAllowed());
    }
}
