<?php

namespace App\Services\Factory\FriendInvitation;

use App\Entity\User;
use App\Entity\Friend;

/**
 *  Manage creating friends invitations
 */
interface FriendInvitationFactoryInterface
{   

    /**
     * create Create friend invitation
     * @param User $inviter User object whose is inviter
     * @param User $invitee User object whose is invitee
     * @return Friend
     */
    public function create(User $inviter, User $invitee): Friend;

}
