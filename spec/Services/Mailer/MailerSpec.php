<?php

namespace spec\App\Services\Mailer;

use App\Services\Mailer\Mailer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use App\Services\Mailer\MailingSystemInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Knp\Snappy\Pdf;
use App\Entity\MovementActivity;
use App\Entity\Workout;
use App\Entity\User;

class MailerSpec extends ObjectBehavior
{
    function let(MailerInterface $mailer, Environment $twig, Pdf $pdf)
    {
        $this->beConstructedWith($mailer, $twig, $pdf);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Mailer::class);
    }

    function it_impelements_mailing_system_interface()
    {
        $this->shouldImplement(MailingSystemInterface::class);
    }

    function it_should_be_able_to_send_reset_password_message($mailer)
    {   
        $user = new User();
        $user
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ;

        $message = $this->sendResetPasswordMessage($user);
        $mailer->send(Argument::any())->shouldBeCalledTimes(1);
        $message->shouldBeAnInstanceOf(TemplatedEmail::class);
        $message->getSubject()->shouldReturn('Reset password!');
        /**
         * @var Address[] $addresses
         */
        $addresses = $message->getTo();
        $addresses->shouldHaveCount(1);
        $addresses[0]->shouldBeAnInstanceOf(Address::class);
        $addresses[0]->getName()->shouldBe('Adam');
        $addresses[0]->getAddress()->shouldBe('exampleuser@fit.com');
    }

    function it_should_be_able_to_send_weekly_report_message($mailer, $twig, $pdf)
    {   
        $user = new User();
        $user
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ;
        $activity = new MovementActivity();
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
        
        $message = $this->sendWeeklyReportMessage($user, $workouts);
        $twig->render(Argument::any(), Argument::any())->shouldBeCalledTimes(1);
        $mailer->send(Argument::any())->shouldBeCalledTimes(1);
        $pdf->getOutputFromHtml(Argument::any())->shouldBeCalledTimes(1);
        $message->shouldBeAnInstanceOf(TemplatedEmail::class);
        $message->getSubject()->shouldReturn('Your weekly report on FitApp!');
        /**
         * @var Address[] $addresses
         */
        $addresses = $message->getTo();
        $addresses->shouldHaveCount(1);
        $addresses[0]->shouldBeAnInstanceOf(Address::class);
        $addresses[0]->getName()->shouldBe('Adam');
        $addresses[0]->getAddress()->shouldBe('exampleuser@fit.com');
    }

}
