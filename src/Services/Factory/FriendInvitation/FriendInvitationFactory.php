<?php

namespace App\Services\Factory\FriendInvitation;

use App\Entity\User;
use App\Entity\Friend;

class FriendInvitationFactory implements FriendInvitationFactoryInterface {
    
    public function create(User $inviter, User $invitee): Friend
    {
        $friendInvitation = new Friend();
        $friendInvitation
            ->setInviter($inviter)
            ->setInvitee($invitee)
            ->setStatus('Pending')
            ->setCreated()
            ;

        return $friendInvitation;
    }
}
