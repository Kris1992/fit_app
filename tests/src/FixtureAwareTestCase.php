<?php
///Not used anymore 
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

abstract class FixtureAwareTestCase extends KernelTestCase
{
    
    private $fixtureExecutor;

    private $fixtureLoader;

    protected function setUp()
    {
        self::bootKernel();
    }

    /**
     * addFixture Add new fixture to load
     * @param FixtureInterface $fixture Fixture object to load
     * @return void
     */
    protected function addFixture(FixtureInterface $fixture): void
    {
        $this->getFixtureLoader()->addFixture($fixture);
    }

    /**
     * executeFixtures Executes all fixtures which has been loaded before
     * @return void
     */
    protected function executeFixtures(): void
    {
        $this->getFixtureExecutor()->execute($this->getFixtureLoader()->getFixtures());
    }

    /**
     * getFixtureExecutor Get ORMExecutor 
     * @return ORMExecutor
     */
    private function getFixtureExecutor()
    {
        if (!$this->fixtureExecutor) {
            $entityManager = self::$container->get(EntityManagerInterface::class);
            $this->fixtureExecutor = new ORMExecutor($entityManager, new ORMPurger($entityManager))
        }

        return $this->fixtureExecutor;
    }

    /**
     * getFixtureLoader Get ContainerAwareLoader object
     * @return ContainerAwareLoader
     */
    private function getFixtureLoader()
    {
        if (!$this->fixtureLoader) {
            $this->fixtureLoader = new ContainerAwareLoader(self::$kernel->getContainer());
        }

        return $this->fixtureLoader;
    }


}