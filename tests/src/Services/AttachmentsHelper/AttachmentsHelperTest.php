<?php

namespace App\Tests\Services\AttachmentsHelper;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\Repository\CuriosityRepository;
use App\Services\AttachmentsHelper\AttachmentsHelperInterface;
use App\Entity\Attachment;
use App\Entity\Curiosity;

class AttachmentsHelperTest extends KernelTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->loadFixtures(array(
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\CuriosityFixtures'
        ));
    }

    public function testIntegrationAddNewAttachments()
    {
        $filenames = ['image0.jpg', 'image1.jpg'];
        $curiosity = $this->getCuriosity();
        $this->addAttachmentsToDb($curiosity, $filenames);

        $attachmentsHelper = self::$container->get(AttachmentsHelperInterface::class);
        $curiosityNew = $attachmentsHelper->addNewAttachments($curiosity, $filenames);
        $attachments = $curiosityNew->getAttachments();

        $this->assertCount(2, $attachments);
    }

    public function testIntegrationRemoveUnusedAttachments()
    {
        $filenames = ['image0.jpg', 'image1.jpg', 'image2.jpg'];
        $curiosity = $this->getCuriosity();
        $this->addAttachmentsToDb($curiosity, $filenames);

        $filenamesNew = ['image0.jpg'];

        $attachmentsHelper = self::$container->get(AttachmentsHelperInterface::class);
        $curiosityNew = $attachmentsHelper->removeUnusedAttachments($curiosity, $filenamesNew);
        $attachments = $curiosityNew->getAttachments();

        $this->assertCount(1, $attachments);
    }

    private function getCuriosity()
    {
        $curiosityRepository = self::$container->get(CuriosityRepository::class);
        $curiosity = $curiosityRepository->findBy(['id' => 1]);

        return $curiosity[0];
    }

    private function addAttachmentsToDb(Curiosity $curiosity, Array $filenames)
    {
        $entityManager = self::$container->get(EntityManagerInterface::class);
        foreach ($filenames as $filename) {
            $attachment = new Attachment();
            $attachment
                ->setCuriosity($curiosity)
                ->setFilename($filename)
            ;
            $entityManager->persist($attachment);
        }
        $entityManager->flush();
    }
}