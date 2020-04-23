<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Services\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use App\Entity\MovementActivity;
use App\Entity\Workout;
use App\Entity\User;
use Knp\Snappy\Pdf;

class MailerTest extends KernelTestCase
{
    public function testIntegrationSendWeeklyReportMessage()
    {
        self::bootKernel();
        $mailerComponent = $this->createMock(MailerInterface::class);
        $mailerComponent->expects($this->once())
            ->method('send')
            ;
        $pdf = self::$container->get(Pdf::class);
        $twig = self::$container->get(Environment::class);

        $user = new User();
        $user
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ;
        $activity = new MovementActivity();
        $activity
            ->setName('Running')
            ;
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setDistanceTotal(10.0)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime())
            ;
        $workouts = array();
        array_push($workouts, $workout);
        array_push($workouts, $workout);

        $mailer = new Mailer($mailerComponent, $twig, $pdf);
        $message = $mailer->sendWeeklyReportMessage($user, $workouts);
        $this->assertCount(1, $message->getAttachments());

    }
}
