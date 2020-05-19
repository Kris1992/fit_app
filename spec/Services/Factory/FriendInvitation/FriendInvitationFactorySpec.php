<?php

namespace spec\App\Services\Factory\FriendInvitation;

use App\Services\Factory\FriendInvitation\FriendInvitationFactory;
use PhpSpec\ObjectBehavior;
use App\Services\Factory\FriendInvitation\FriendInvitationFactoryInterface;
use App\Entity\Friend;
use App\Entity\User;

class FriendInvitationFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FriendInvitationFactory::class);
    }

    function it_impelements_friend_invitation_factory_interface()
    {
        $this->shouldImplement(FriendInvitationFactoryInterface::class);
    }

    function it_should_be_able_to_create_friend()
    {
        $user = new User();
        $user2 = new User();

        $friend = $this->create($user, $user2);
        $friend->shouldBeAnInstanceOf(Friend::class);
        $friend->getInviter()->shouldBeAnInstanceOf(User::class);
        $friend->getInvitee()->shouldBeAnInstanceOf(User::class);
        $friend->getStatus()->shouldReturn('Pending');
        $friend->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
    }

}
