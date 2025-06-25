<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CompositeUserChecker implements UserCheckerInterface
{
    private array $userCheckers;

    public function __construct(UserCheckerInterface ...$userCheckers)
    {
        $this->userCheckers = $userCheckers;
    }
    public function checkPreAuth(UserInterface $user): void
    {
        foreach ($this->userCheckers as $userChecker) {
            $userChecker->checkPreAuth($user);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        foreach ($this->userCheckers as $userChecker) {
            $userChecker->checkPostAuth($user);
        }
    }
}