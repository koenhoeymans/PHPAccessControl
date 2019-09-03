<?php

namespace PHPAccessControl\Specification;

use PHPAccessControl\AccessControledObject\Aco;

class InMemoryInheritanceListTest extends \PHPUnit\Framework\TestCase
{
	public function setup()
	{
		$this->inheritanceList = new InMemoryInheritanceList();
	}

	/**
	 * @test
	 */
	public function keepsTrackOfAddedParent()
	{
		// given
		$child = new Aco('catX');
		$parent = new Aco('catY');
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
		$child = new Aco('catX');
		$parent = new Aco('catY');
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
		$child = new Aco('catX');
		$parentA = new Aco('catY');
		$parentB = new Aco('catZ');
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
		$child = new Aco('child');
		$parent = new Aco('parent');
		$greatParent = new Aco('greatParent');
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
		$acoX = new Aco('x');
		$acoY = new Aco('y');
		$acoZ = new Aco('z');
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
		$acoX = new Aco('x');
		$this->inheritanceList->addParent($acoX, $acoX);

		// when
		$parentsOfX = $this->inheritanceList->getParentsRecursively($acoX);

		// then
		$this->assertEquals(array(), $parentsOfX);
	}
}
