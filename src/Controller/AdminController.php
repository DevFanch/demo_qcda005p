<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    #[IsGranted('ROLE_DIRECTOR', message: 'Vous n\'avez pas les permissions pour accéder à cette page')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/course', name: 'course', methods: ['GET'])]
    #[IsGranted('ROLE_DIRECTOR', message: 'Vous n\'avez pas les permissions pour administrer les cours')]
    public function course(CourseRepository $courseRepository): Response
    {
        return $this->render('admin/course.html.twig', [
            'courses' => $courseRepository->findBy([], ['dateCreated' => 'DESC'])
        ]);
    }

    #[Route('/course/{id}/status', name: 'course_status', methods: ['POST'])]
    #[IsGranted('ROLE_DIRECTOR', message: 'Vous n\'avez pas les permissions pour modifier le statut des cours')]
    public function courseStatus(Course $course, EntityManagerInterface $entityManager): Response
    {
        // On inverse le statut publié/non publié
        $nouveauStatut = !$course->isPublished();
        $course->setPublished($nouveauStatut);

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'id' => $course->getId(),
            'published' => $nouveauStatut,
            'message' => $nouveauStatut 
                ? 'Le cours a été publié avec succès.' 
                : 'Le cours a été dépublié avec succès.'
        ]);
    }

    #[Route('/course/{id}/delete', name: 'course_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted("ROLE_DIRECTOR", "course", message: 'Vous n\'êtes pas autorisé à supprimer ce cours')]
    public function delete(Course $course, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-' . $course->getId(), $request->get('token'))) {
            try {
                $em->remove($course);
                $em->flush();
                $this->addFlash('success', 'Le cours a été supprimé');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Le cours n\'a pu être supprimé');
            }
        }
        return $this->redirectToRoute('admin_course');
    }

    #[Route('/course-status', name: 'course_status')]
    #[IsGranted('ROLE_DIRECTOR', message: 'Vous n\'avez pas les permissions de visualiser le statut des cours : cron')]
    public function status(): Response
    {
        return $this->render('admin/course_status.html.twig');
    }
}
