<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Services\FoldersManager\FoldersManagerInterface;
use App\Services\ImagesManager\ImagesConstants;
use App\Services\AttachmentsManager\AttachmentsManager;

class UploadedImagesRemoveCommand extends Command
{
    protected static $defaultName = 'app:uploaded-images:remove';
    private $foldersManager;
    private $uploadsDirectory;

    public function __construct(FoldersManagerInterface $foldersManager, string $uploadsDirectory)
    {
        parent::__construct(null);
        $this->foldersManager = $foldersManager;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    protected function configure()
    {
        $this
            ->setDescription('Remove all uploaded images (uploaded path depends of env)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $usersImagesPath = $this->uploadsDirectory.'/'.ImagesConstants::USERS_IMAGES;
        $workoutsImagesPath = $this->uploadsDirectory.'/'.ImagesConstants::WORKOUTS_IMAGES;
        $curiositiesImagesPath = $this->uploadsDirectory.'/'.ImagesConstants::CURIOSITIES_IMAGES;
        $attachmentsPath = $this->uploadsDirectory.'/'.AttachmentsManager::ATTACHMENTS_DIR;

        $this->foldersManager->deleteFolder($usersImagesPath);
        $this->foldersManager->deleteFolder($workoutsImagesPath);
        $this->foldersManager->deleteFolder($curiositiesImagesPath);
        $this->foldersManager->deleteFolder($attachmentsPath);

        $io->success('All uploaded images was removed successfully.');

        return 0;
    }
}
