<?php

namespace App\Tests\Behat;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Class Responsible to web(redirects etc.) part of context
 */
class WebContext extends RawMinkContext
{


     /**
     * @When I wait for the page to be loaded
     */
    public function iWaitForThePageToBeLoaded()
    {
        //Time to redirect
        sleep(1);
        $this->getSession()->wait(10000, "document.readyState === 'complete'");
    }

    /**
     * @When I wait for the modal to be loaded
     */
    public function iWaitForTheModalToBeLoaded()
    {
        $this->getSession()->wait(
            5000,
            "$('.modal:visible').length > 0"
        );
    }

    /**
     * @When I confirm the popup
     */
    public function iConfirmThePopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }
}
