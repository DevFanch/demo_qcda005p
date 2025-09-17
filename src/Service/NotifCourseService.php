<?php

namespace App\Service;

use App\Entity\Course;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NotifCourseService
{
    public function __construct(private readonly MailerInterface $mailer) {}

    public function notifyAdminNewCourse(Course $course, UserInterface $user): void
    {
        $email = (new TemplatedEmail())
            ->from($user->getEmail())
            ->to('admin@admin.com')
            ->subject('New course created')
            ->htmlTemplate('email/new_course.html.twig')
            ->context(['course' => $course]);
        $this->mailer->send($email);
    }
}
