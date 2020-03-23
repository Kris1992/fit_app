<?php

namespace App\Twig;

use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Services\ImagesManager\ImagesManagerInterface;



class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public static function getSubscribedServices()
    {
        return [
            ImagesManagerInterface::class
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'getUploadedAssetPath'])
        ];
    }
    public function getUploadedAssetPath(string $path): string
    {
        return $this->container
            ->get(ImagesManagerInterface::class)
            ->getPublicPath($path);
        
    }
}
