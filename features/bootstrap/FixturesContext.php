<?php

use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
//use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Doctrine\Common\DataFixtures\Loader;

/*
                            Use this Context in features needed fixtures data
 */

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FixturesContext extends HelperContext
{

    /**
     * @Given Is database with :typeObject
     */
    public function isDatabaseWith($typeObject)
    {
        switch ($typeObject) {
            case 'activities':
                $activitiesFixtures = ['BodyweightActivity', 'MovementActivity', 'MovementSetActivity', 'WeightActivity'];
                $this->loadFixtures($activitiesFixtures);
                break;
            case 'workouts':
                $this->loadFixtures(['User']);
                break;
            default:
                break;
        }
    }

    private function loadFixtures($fixturesNames)
    {
        $loader = new Loader();
        foreach ($fixturesNames as $fixture) {
            $loader->loadFromFile(__DIR__.'/../../src/DataFixtures/'.$fixture.'Fixtures.php');
        }

        $executor = new ORMExecutor($this->getEntityManager());
        $executor->execute($loader->getFixtures(), true);

        //$loader = new ContainerAwareLoader($this->kernel->getContainer());
        //$loader->loadFromDirectory(__DIR__.'/../../src/DataFixtures');
        //$executor = new ORMExecutor($this->getEntityManager());
        //$executor->execute($loader->getFixtures(), true);
    }
    /**
     *BeforeScenario @fixtures
     */
    /*public function loadFixtures()
    {
        $loader = new ContainerAwareLoader($this->kernel->getContainer());
        $loader->loadFromDirectory(__DIR__.'/../../src/DataFixtures');
        $executor = new ORMExecutor($this->getEntityManager());
        $executor->execute($loader->getFixtures(), true);
    }*/

}
