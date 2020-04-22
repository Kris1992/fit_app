<?php

namespace App\Services\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\User;

/**
 * Manage sending emails
 */
interface MailingSystemInterface
{   

    /**
     * sendResetPasswordMessage Sending email with reset password message
     * @param  User   $user User whose want reset password
     * @return 
     */
    public function sendResetPasswordMessage(User $user): TemplatedEmail;

    /**
     * sendWeeklyReportMessage Sending email with last week workouts report to user
     * @param  User   $user     User whose will get email with report
     * @param  array  $workouts Workouts from last week
     * @return
     */
    public function sendWeeklyReportMessage(User $user, array $workouts): TemplatedEmail;

}
