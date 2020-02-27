<?php

use Behat\MinkExtension\Context\RawMinkContext;


/**
 * Class responsible for taking screenshot
 */
class SnapshotContext extends RawMinkContext
{

    /**
     * Saving a screenshot
     *
     * @When I save a screenshot to :filename
     */
    public function iSaveAScreenshotIn($filename)
    {
        sleep(1);
        $this->createDirNamed('testsScreens');
        $this->saveScreenshot($filename, __DIR__.'/../../testsScreens');
    }

    private function createDirNamed($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
        }
    }

}
