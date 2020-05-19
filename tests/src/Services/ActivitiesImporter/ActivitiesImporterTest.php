<?php

namespace App\Tests\Services\ActivitiesImporter;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Services\ActivitiesImporter\ActivitiesImporterInterface;
use App\Services\ActivitiesImporter\ActivitiesImporter;
use Psr\Log\LoggerInterface;
use App\Services\FilesManager\FilesManagerInterface;
use App\Services\FileReader\FileReaderInterface;
use App\Services\ModelValidator\ModelValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use App\Repository\AbstractActivityRepository;

class ActivitiesImporterTest extends KernelTestCase
{
    //Clear db before test
    protected function setUp(): void
    {
        self::bootKernel();
        $purger = new ORMPurger(self::$container->get(EntityManagerInterface::class));
        $purger->purge();
    }

    public function testIntegrationImportActivitiesFromValidCSV()
    {
        
        $logger = self::$container->get(LoggerInterface::class);
        $uploadedFile = new UploadedFile(
            __DIR__.'/../../../../build/testsData/csv/valid_csv.txt',
            'valid.csv'
        );

        $filesManager = self::$container->get(FilesManagerInterface::class);
        $csvFileReader = self::$container->get(FileReaderInterface::class);
        $modelValidator = self::$container->get(ModelValidatorInterface::class);
        $entityManager = self::$container->get(EntityManagerInterface::class);

        $activitiesImporter = new ActivitiesImporter($logger, $filesManager, $csvFileReader, $modelValidator, $entityManager, self::$container->getParameter('uploads_directory'));
        $result = $activitiesImporter->import($uploadedFile);

        $this->assertEquals(2, $result['valid']);
        $this->assertEquals(0, $result['invalid']);
        $this->assertCount(0, $result['invalidRows']);

        $activityRepository = self::$container->get(AbstractActivityRepository::class);
        $this->assertCount(2, $activityRepository->findAll());
    }

    public function testIntegrationImportActivitiesFromPartialValidCSV()
    {
        
        $logger = self::$container->get(LoggerInterface::class);
        $uploadedFile = new UploadedFile(
            __DIR__.'/../../../../build/testsData/csv/half_valid_csv.txt',
            'valid.csv'
        );

        $filesManager = self::$container->get(FilesManagerInterface::class);
        $csvFileReader = self::$container->get(FileReaderInterface::class);
        $modelValidator = self::$container->get(ModelValidatorInterface::class);
        $entityManager = self::$container->get(EntityManagerInterface::class);

        $activitiesImporter = new ActivitiesImporter($logger, $filesManager, $csvFileReader, $modelValidator, $entityManager, self::$container->getParameter('uploads_directory'));
        $result = $activitiesImporter->import($uploadedFile);

        $this->assertEquals(1, $result['valid']);
        $this->assertEquals(1, $result['invalid']);
        $this->assertCount(1, $result['invalidRows']);
        $this->assertEquals('The activity with the same name and intensity already exist', $result['invalidRows'][0]['message']);

        $activityRepository = self::$container->get(AbstractActivityRepository::class);
        $this->assertCount(1, $activityRepository->findAll());
    }

}