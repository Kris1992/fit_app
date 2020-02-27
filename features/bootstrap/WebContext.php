<?php

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Class Responsible to web(redirects etc.) part of context
 */
class WebContext extends RawMinkContext
{
     /**
     * @Given I wait for the page to be loaded
     */
    public function iWaitForThePageToBeLoaded()
    {
        //Time to redirect
        sleep(1);
        $this->getSession()->wait(10000, "document.readyState === 'complete'");
    }




}
