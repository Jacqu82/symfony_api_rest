<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const SHOW = 'show';
    const EDIT = 'edit';

    private $accessDecision;

    public function __construct(AccessDecisionManagerInterface $accessDecision)
    {
        $this->accessDecision = $accessDecision;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::SHOW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->accessDecision->decide($token, [User::ROLE_ADMIN])) {
            return true;
        }

        switch ($attribute) {
            case self::SHOW:
            case self::EDIT:
                return $this->isUserHimself($subject, $token);
        }

        return false;
    }

    /**
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function isUserHimself($subject, TokenInterface $token): bool
    {
        $authenticatedUser = $token->getUser();
        if (!$authenticatedUser instanceof User) {
            return false;
        }

        /** @var User $user */
        $user = $subject;

        return $authenticatedUser->getId() === $user->getId();
    }
}
