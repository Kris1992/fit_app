<?php

namespace App\MessageHandler\Command;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteWorkoutImage;
use App\Services\ImagesManager\ImagesManagerInterface;

class DeleteWorkoutImageHandler implements  MessageSubscriberInterface
{

    private $workoutsImagesManager;

    public function __construct(ImagesManagerInterface $workoutsImagesManager)
    {
        $this->workoutsImagesManager = $workoutsImagesManager;
    }

    public function __invoke(DeleteWorkoutImage $deleteWorkoutImage)
    {
        $subdirectory = $deleteWorkoutImage->getSubdirectory();
        $filename = $deleteWorkoutImage->getFilename();

        $isDeleted = $this->workoutsImagesManager->deleteImage($filename, $subdirectory);
        if (!$isDeleted) {
            throw new \Exception(sprintf('Cannot delete wokout image "%s" from directory "%s"!!', $filename, $subdirectory));
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield DeleteWorkoutImage::class => [
            'method' => '__invoke',
        ];
    }
}