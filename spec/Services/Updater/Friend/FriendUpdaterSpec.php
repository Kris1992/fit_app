<?php

namespace spec\App\Services\Updater\Friend;

use App\Services\Updater\Friend\FriendUpdaterInterface;
use App\Services\Updater\Friend\FriendUpdater;
use PhpSpec\ObjectBehavior;
use App\Entity\Friend;
use App\Entity\User;

class FriendUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FriendUpdater::class);
    }

    function it_impelements_friend_updater_interface()
    {
        $this->shouldImplement(FriendUpdaterInterface::class);
    }

    function it_should_be_able_to_update_friend_by_accept()
    {
        $user = new User();
        $user
            ->setEmail('user@fit.com')
            ;
        $user2 = new User();
        $user2
            ->setEmail('user2@fit.com')
            ;
        $friend = new Friend();
        $friend
            ->setInviter($user)
            ->setInvitee($user2)
            ->setStatus('Pending')
            ->setCreated()
            ;

        $friend = $this->update($friend, 'accept');
        $friend->shouldBeAnInstanceOf(Friend::class);
        $friend->getInviter()->shouldBeAnInstanceOf(User::class);
        $friend->getInviter()->getEmail()->shouldReturn('user@fit.com');
        $friend->getInvitee()->shouldBeAnInstanceOf(User::class);
        $friend->getInvitee()->getEmail()->shouldReturn('user2@fit.com');
        $friend->getStatus()->shouldReturn('Accepted');
        $friend->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
    }

    function it_should_be_able_to_update_friend_by_reject()
    {
        $user = new User();
        $user
            ->setEmail('user@fit.com')
            ;
        $user2 = new User();
        $user2
            ->setEmail('user2@fit.com')
            ;
        $friend = new Friend();
        $friend
            ->setInviter($user)
            ->setInvitee($user2)
            ->setStatus('Pending')
            ->setCreated()
            ;

        $friend = $this->update($friend, 'reject');
        $friend->shouldBeAnInstanceOf(Friend::class);
        $friend->getInviter()->shouldBeAnInstanceOf(User::class);
        $friend->getInviter()->getEmail()->shouldReturn('user@fit.com');
        $friend->getInvitee()->shouldBeAnInstanceOf(User::class);
        $friend->getInvitee()->getEmail()->shouldReturn('user2@fit.com');
        $friend->getStatus()->shouldReturn('Rejected');
        $friend->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
    }

    function it_should_not_update_friend_with_unsupported_data()
    {
        $user = new User();
        $user
            ->setEmail('user@fit.com')
            ;
        $user2 = new User();
        $user2
            ->setEmail('user2@fit.com')
            ;
        $friend = new Friend();
        $friend
            ->setInviter($user)
            ->setInvitee($user2)
            ->setStatus('Pending')
            ->setCreated()
            ;

        $friend = $this->update($friend, 'test');
        $friend->shouldBeAnInstanceOf(Friend::class);
        $friend->getInviter()->shouldBeAnInstanceOf(User::class);
        $friend->getInviter()->getEmail()->shouldReturn('user@fit.com');
        $friend->getInvitee()->shouldBeAnInstanceOf(User::class);
        $friend->getInvitee()->getEmail()->shouldReturn('user2@fit.com');
        $friend->getStatus()->shouldReturn('Pending');
        $friend->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
    }

}
