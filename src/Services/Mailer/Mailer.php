<?php

namespace App\Services\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use App\Entity\User;
use Knp\Snappy\Pdf;

/**
 * Service responsible for sending emails
 */
class Mailer implements MailingSystemInterface
{
    private $mailer;
    private $twig;
    private $pdf;

    public function __construct(MailerInterface $mailer, Environment $twig, Pdf $pdf)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    public function sendResetPasswordMessage(User $user): TemplatedEmail
    {
        $message = (new TemplatedEmail())
            ->from(new Address('krakowdev01@gmail.com', 'FitApp'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject('Reset password!')
            ->htmlTemplate('emails/reset_password_email.inky.twig')
            ->context([
                'user' => $user,
            ]);
        $this->mailer->send($message);

        //To make tests
        return $message;
    }

    public function sendWeeklyReportMessage(User $user, array $workouts): TemplatedEmail
    {
        $durationAllTotal = 0;
        $distanceAllTotal = 0;
        $energyAllTotal = 0;

        foreach ($workouts as $workout) {
            $durationAllTotal += $workout->getDurationSecondsTotal(); 
            $energyAllTotal += $workout->getBurnoutEnergyTotal();
            if ($workout->getDistanceTotal()) {
                $distanceAllTotal += $workout->getDistanceTotal();
            } 
        }

        $html = $this->twig->render('emails/user_weekly_report_pdf.html.twig', [
            'workouts' => $workouts,
            'durationAllTotal' => $durationAllTotal,
            'distanceAllTotal' => $distanceAllTotal,
            'energyAllTotal' => $energyAllTotal
        ]);

        $pdf = $this->pdf->getOutputFromHtml($html);
        $message = (new TemplatedEmail())
            ->from(new Address('krakowdev01@gmail.com', 'FitApp'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject('Your weekly report on FitApp!')
            ->htmlTemplate('emails/user_weekly_report.inky.twig')
            ->context([
                'user' => $user,
                'workouts' => $workouts,
                'durationAllTotal' => $durationAllTotal,
                'distanceAllTotal' => $distanceAllTotal,
                'energyAllTotal' => $energyAllTotal
            ])
            ->attach($pdf, sprintf('weekly_report_%s.pdf', date('Y-m-d')));
        $this->mailer->send($message);

        //To make tests
        return $message;
    }

}



