<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpKernel\KernelInterface;

final class DemoContext implements Context
{
    /** @var KernelInterface */
    private $kernel;
    
    public function __construct(KernelInterface $kernel) 
    {
        $this->kernel = $kernel;
    }

    /**
     * @Then the application's kernel should use :expected environment 
     */
    public function kernelEnvironmentShouldBe(string $expected): void
    {
        if ($this->kernel->getEnvironment() !== $expected) {
            throw new \RuntimeException();
        }
    }
}