<?php

namespace App\Security\Voter;

use App\Entity\Course;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CourseVoter extends Voter
{
    public const EDIT = 'course_edit';
    public const VIEW = 'course_view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Course;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            default => false,
        };
    }
    
    private function canEdit(Course $course, UserInterface $user): bool
    {
        return $user === $course->getAuthor() || in_array('ROLE_ADMIN', $user->getRoles());
    }
    
    private function canView(Course $course, UserInterface $user): bool
    {
        return true;
    }
}
