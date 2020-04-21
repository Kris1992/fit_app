<?php

namespace App\Services\Mailer;

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
    public function sendResetPasswordMessage(User $user);

}
