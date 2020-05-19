<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Use this Context in features needed fixtures data
 */

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FixturesContext extends WebTestCase implements Context
{
    use FixturesTrait;

    public function __construct()
    {
      parent::__construct();
    }

    /**
     * @Given Is database with :typeObject
     */
    public function isDatabaseWith($typeObject)
    {
        switch ($typeObject) {
            case 'activities':
                $fixturesNames = ['BodyweightActivityFixtures', 'MovementActivityFixtures', 'MovementSetActivityFixtures', 'WeightActivityFixtures'];
                $this->loadFixturesFromNames($fixturesNames);
                break;
            case 'workouts':
                $fixturesNames = ['BodyweightActivityFixtures', 'MovementActivityFixtures', 'MovementSetActivityFixtures', 'WeightActivityFixtures', 'UserFixtures', 'WorkoutFixtures'];
                $this->loadFixturesFromNames($fixturesNames);
                break;
            case 'users':
                $this->loadFixturesFromNames(['UserFixtures']);
                break;
            case 'curiosities':
                $this->loadFixturesFromNames(['UserFixtures', 'CuriosityFixtures']);
                break;
            default:
                break;
        }
    }

    private function loadFixturesFromNames($fixturesNames)
    {   
        $fixtures = [];
        foreach ($fixturesNames as $fixture) {
            array_push($fixtures, sprintf('App\DataFixtures\\%s', $fixture));
        }
        $this->loadFixtures($fixtures);
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
