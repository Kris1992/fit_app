<?php
declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Repository\FriendRepository;
use App\Entity\User;

//Adds count of invites to friends (for now maybe in future that will be expanded)
class NotificationExtension extends AbstractExtension
{
    private $friendRepository;
    
    public function __construct(FriendRepository $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    public function getFunctions(): Array
    {
        return [
            new TwigFunction(
                'notifications',
                [$this, 'getNotificationsCount'],
                ['needs_environment' => false]
            ),
        ];
    }

    public function getNotificationsCount(User $currentUser): int
    {
        return $this->friendRepository->countInvitationsByUser($currentUser);
    }


}
