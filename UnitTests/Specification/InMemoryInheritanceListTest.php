<?php

require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'TestHelper.php';
require_once dirname(__FILE__)
	. DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'Support'
	. DIRECTORY_SEPARATOR . 'AcoClasses.php';

class PHPAccessControl_Specification_InMemoryInheritanceListTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->inheritanceList = new \PHPAccessControl\Specification\InMemoryInheritanceList();
	}

	/**
	 * @test
	 */
	public function keepsTrackOfAddedParent()
	{
		// given
		$child = CreateAco::name('catX');
		$parent = CreateAco::name('catY');
		$this->inheritanceList->addParent($parent, $child);

		// when
		$parents = $this->inheritanceList->getParents($child);

		// then
		$this->assertEquals(array($parent), $parents);
	}

	/**
	 * @test
	 */
	public function storesParentOnlyOnce()
	{
		// given
		$child = CreateAco::name('catX');
		$parent = CreateAco::name('catY');
		$this->inheritanceList->addParent($parent, $child);
		$this->inheritanceList->addParent($parent, $child);

		// when
		$parents = $this->inheritanceList->getParents($child);

		// then
		$this->assertEquals(array($parent), $parents);
	}

	/**
	 * @test
	 */
	public function multipleParentsCanBeSpecified()
	{
		// given
		$child = CreateAco::name('catX');
		$parentA = CreateAco::name('catY');
		$parentB = CreateAco::name('catZ');
		$this->inheritanceList->addParent($parentA, $child);
		$this->inheritanceList->addParent($parentB, $child);

		// when
		$parents = $this->inheritanceList->getParents($child);

		// then
		$this->assertEquals(array($parentA, $parentB), $parents);
	}

	/**
	 * @test
	 */
	public function retrievesAllParentsRecursiveInTree()
	{
		// given
		$child = CreateAco::name('child');
		$parent = CreateAco::name('parent');
		$greatParent = CreateAco::name('greatParent');
		$this->inheritanceList->addParent($greatParent, $parent);
		$this->inheritanceList->addParent($parent, $child);

		// when
		$parents = $this->inheritanceList->getParentsRecursively($child);

		// then
		$this->assertEquals(array($parent, $greatParent), $parents);
	}

	/**
	 * @test
	 */
	public function circularInheritanceDoesNotCauseTheSkyToFallDown()
	{
		// given
		$acoX = CreateAco::name('x');
		$acoY = CreateAco::name('y');
		$acoZ = CreateAco::name('z');
		$this->inheritanceList->addParent($acoX, $acoY);
		$this->inheritanceList->addParent($acoY, $acoZ);
		$this->inheritanceList->addParent($acoZ, $acoX);

		// when
		$parentsOfX = $this->inheritanceList->getParentsRecursively($acoX);
		$parentsOfY = $this->inheritanceList->getParentsRecursively($acoY);
		$parentsOfZ = $this->inheritanceList->getParentsRecursively($acoZ);

		// then
		$this->assertEquals(array($acoY, $acoX), $parentsOfZ);
		$this->assertEquals(array($acoX, $acoZ), $parentsOfY);
		$this->assertEquals(array($acoZ, $acoY), $parentsOfX);
	}

	/**
	 * @test
	 */
	public function specificationDoesNotInheritFromItsOwn()
	{
				// given
		$acoX = CreateAco::name('x');
		$this->inheritanceList->addParent($acoX, $acoX);

		// when
		$parentsOfX = $this->inheritanceList->getParentsRecursively($acoX);

		// then
		$this->assertEquals(array(), $parentsOfX);
	}
}