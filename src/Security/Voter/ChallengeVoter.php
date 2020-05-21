<?php
declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Challenge;
use Symfony\Component\Security\Core\Security;

class ChallengeVoter extends Voter
{
    /** to check user role inside voter**/
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['MANAGE'])
            && $subject instanceof Challenge;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Challenge $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'MANAGE':
                if($this->security->isGranted('ROLE_ADMIN')) {
                    $today = new \DateTime();
                    if ($subject->getStartAt() < $today && $subject->getStopAt() > $today) {
                        return false;
                    }
                    
                    return true;
                }
                break;
        }

        return false;
    }
}
