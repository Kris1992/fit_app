<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;

use Behat\Behat\Context\SnippetAcceptingContext;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $purger = new ORMPurger($this->kernel->getContainer()->get('doctrine')->getManager());
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
        
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->currentUser = $this->thereIsAnAdminUserWithPassword('admin0@fit.com', 'admin01');
        $this->visitPath('/login');

        $this->getPage()->fillField('email', 'admin0@fit.com');
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

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }
}
