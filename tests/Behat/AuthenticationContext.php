<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Behat\MinkExtension\Context\RawMinkContext;

use Behat\Behat\Context\SnippetAcceptingContext;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
//use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

use \PHPUnit\Framework\Assert as Assertions;
//require_once __DIR__.'/../../bin/.phpunit/phpunit-7.5-0/src/Framework/Assert/Functions.php';


/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class AuthenticationContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var currentUser
     */
    private $currentUser;

    public function __construct(
        KernelInterface $kernel, 
        UserPasswordEncoderInterface $passwordEncoder 
    )
    {
        $this->kernel = $kernel;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * @Given there is an admin user :email with password :password
     */
    public function thereIsAnAdminUserWithPassword($email, $password)
    {
     
        $user = new \App\Entity\User();
        $user
            ->setEmail($email)
            ->setFirstName('Admin')
            ->setSecondName('Admin')
            ->setGender('male')
            ->setRoles([('ROLE_ADMIN')])
            ->agreeToTerms()
            ; 
        $user->saveLogin();
        $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $password
            ));
        
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->currentUser = $this->thereIsAnAdminUserWithPassword('admin0Test@fit.com', 'admin01');
        $this->visitPath('/login');

        $this->getPage()->fillField('email', 'admin0Test@fit.com');
        $this->getPage()->fillField('password', 'admin01');
        $this->getPage()->pressButton('Sign in');        
    }

    /**
     * @When I click :linkName
     */
    public function iClick($linkName)
    {
        $this->getPage()->clickLink($linkName);
    }

     /**
     * Check is number of items inside table
     * 
     * @Then I should see :count row(s) in the table
     */
    public function iShouldSeeRowsInTheTable($count)
    {
        $table = $this->getPage()->find('css', 'table.table');
        Assertions::assertNotNull($table, 'Cannot find a table!');
        Assertions::assertCount(intval($count), $table->findAll('css', 'tbody tr'));
    }

     /**
     * @When I press :linkName in the row with name :name
     */
    public function iPressInTheRowWithName($linkName, $name)
    {
        $linkRow = $this->findRowByText($name);
        Assertions::assertNotNull($linkRow, 'Cannot find proper link in the table row!');
        $linkRow->clickLink($linkName);
    }

     /**
     * @When I press :linkName in the row with name :name and :intensity intensity
     */
    public function iPressInTheRowWithNameAndIntensity($linkName, $name, $intensity)
    {
        $linkRow = $this->findRowByNameAndIntensity($name, $intensity);
        Assertions::assertNotNull($linkRow, 'Cannot find proper link in the table row!');
        $linkRow->clickLink($linkName);
    }

    /**
     * @When I check :checkName in the row with name :name and :intensity intensity
     */
    public function iCheckInTheRowWithNameAndIntensity($checkName, $name, $intensity)
    {
        //$option = $this->fixStepArgument($checkName);
        $row = $this->findRowByNameAndIntensity($name, $intensity);
        Assertions::assertNotNull($row, 'Cannot find proper checkbox in the table row!');
        $rowData = explode(' ', $row->getText());
        $checkbox = $this->findCheckboxWithNameAndValue($checkName, $rowData[0]);
        Assertions::assertNotNull($checkbox, 'Cannot find proper checkbox with this name!');
        $checkbox->check();
    }

    /**
     * @When I check :checkName in the row with name :name
     */
    public function iCheckInTheRowWithName($checkName, $name)
    {
        $row = $this->findRowByText($name);
        $rowData = explode(' ', $row->getText());
        $checkbox = $this->findCheckboxWithNameAndValue($checkName, $rowData[0]);
        Assertions::assertNotNull($checkbox, 'Cannot find proper checkbox with this name!');
        $checkbox->check();
    }


    /**
     * @Then I check first unchecked :checkName in the row with name :name
     */
    public function iCheckFirstUncheckedInTheRowWithName($checkName, $name)
    {
        $rows = $this->findRowsByText($name);
        $row = $this->findRowWithUncheckedCheckbox($rows, $checkName);
        Assertions::assertNotNull($row, 'Cannot find proper unchecked checkbox in the table row!');
        $rowData = explode(' ', $row->getText());
        $checkbox = $this->findCheckboxWithNameAndValue($checkName, $rowData[0]);
        Assertions::assertNotNull($checkbox, 'Cannot find proper checkbox with this name!');
        $checkbox->check();
    }

    private function findRowWithUncheckedCheckbox($rows, $checkName)
    {
        foreach ($rows as $row) {
            $checkbox = $row->find('css', sprintf(
                'input[type=checkbox][name="%s"]', 
                $checkName)
            );
            if (!$checkbox->isChecked()) {
               return $row; 
            }
        }
        return null;
    }

    private function findCheckboxWithNameAndValue($checkName, $value)
    {
        $checkboxes = $this->getPage()->findAll('css', sprintf('input[type=checkbox][name="%s"]', $checkName));
        Assertions::assertNotEmpty($checkboxes, 'Cannot find any checkbox with this name!');
        foreach ($checkboxes as $checkbox) {
            if ($checkbox->getAttribute('value') == $value) {
                return $checkbox;
            }
        }
        return null;
    }

    /**
     * @param $rowText
     * @return \Behat\Mink\Element\NodeElement
     */
    private function findRowsByText($rowText)
    {
        $rows = $this->getPage()->findAll('css', sprintf('table tr:contains("%s")', $rowText));
        Assertions::assertNotEmpty($rows, 'Cannot find a table row with this text!');
        
        return $rows;
    }

     /**
     * @param $rowText
     * @return \Behat\Mink\Element\NodeElement
     */
    private function findRowByText($rowText)
    {
        $row = $this->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        Assertions::assertNotNull($row, 'Cannot find a table row with this text!');
        
        return $row;
    }

    private function findRowByNameAndIntensity($name, $intensity)
    {
        $rows = $this->findRowsByText($name);
        foreach ($rows as $row) {
            $rowCells = $row->getText();
            if (preg_match('/\b'.$intensity.'\b/', $rowCells)) {
                return $row;
            }
        }
        return null;
    }












    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) == '') {}
        fwrite(STDOUT, "\033[u");
        return;
    }

    ///To remove from here (WebContext)
    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine')->getManager();
    }
}
