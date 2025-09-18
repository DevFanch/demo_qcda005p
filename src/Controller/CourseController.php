<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\TrainerRepository;
use App\Service\NotifCourseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/course', name: 'course_')]
final class CourseController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findLastPublishedCourses();
        return $this->render('course/list.html.twig', [
            'courses' => $courses,
        ]);
    }
    #[Route('/{id}', name: 'show', requirements:['id'=>'\d+'], methods: ['GET'])]
    public function show(Course $course,CourseRepository $courseRepository): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/ajouter', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request,EntityManagerInterface $em, NotifCourseService $notifCourseService): Response
    {
        $course = new Course();
        $courseForm = $this->createForm(CourseType::class, $course);
        $courseForm->handleRequest($request);
        if($courseForm->isSubmitted() && $courseForm->isValid()){
            //Set Current User as Author
            $course->setAuthor($this->getUser());
            $em->persist($course);
            $em->flush();

            //Notify Admin
            $notifCourseService->notifyAdminNewCourse($course, $this->getUser());
            
            //Add Flash Message
            $this->addFlash('success','Le cours a été ajouté');
            return $this->redirectToRoute('course_show', ['id'=>$course->getId()]);
        }

        return $this->render('course/create.html.twig', [
            'courseForm'=>$courseForm,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements:['id'=>'\d+'], methods: ['GET','POST'])]
    // #[IsGranted("course_edit", "course", message: 'Vous n\'êtes pas autorisé à modifier ce cours')]
    public function edit(Course $course, Request $request,EntityManagerInterface $em): Response
    {
        // Check if the user is authorized to edit the course by using the voter
        $this->denyAccessUnlessGranted('course_edit', $course, 'Vous n\'êtes pas autorisé à modifier ce cours');

        $courseForm = $this->createForm(CourseType::class, $course);
        $courseForm->handleRequest($request);
        if($courseForm->isSubmitted() && $courseForm->isValid()){
            $em->flush();
            $this->addFlash('success','Le cours a été modifié');
            return $this->redirectToRoute('course_show', ['id'=>$course->getId()]);
        }
        return $this->render('course/edit.html.twig', [
            'courseForm'=>$courseForm

        ]);
    }

    #[Route('/{id}/formateurs', name: 'trainers', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted("ROLE_PLANNER")]
    public function trainers(Course $course, TrainerRepository $repository): Response
    {
        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'trainers' => $course->getTrainers()
        ]);
    }
}






















