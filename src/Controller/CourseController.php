<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\TrainerRepository;
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
        $courses = $courseRepository->findLastCourses();
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
    public function create(Request $request,EntityManagerInterface $em): Response
    {
        $course = new Course();
        $courseForm = $this->createForm(CourseType::class, $course);
        $courseForm->handleRequest($request);
        if($courseForm->isSubmitted() && $courseForm->isValid()){
            $em->persist($course);
            $em->flush();

            $this->addFlash('success','Le cours a été ajouté');
            return $this->redirectToRoute('course_show', ['id'=>$course->getId()]);
        }

        return $this->render('course/create.html.twig', [
            'courseForm'=>$courseForm,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', requirements:['id'=>'\d+'], methods: ['GET','POST'])]
    public function edit(Course $course, Request $request,EntityManagerInterface $em): Response
    {
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

    #[Route('/{id}/supprimer', name: 'delete', requirements:['id'=>'\d+'], methods: ['GET'])]
    public function delete(Course $course, Request $request,EntityManagerInterface $em): Response
    {
        if($this->isCsrfTokenValid('delete-'.$course->getId(), $request->get('token'))){
            try{
                $em->remove($course);
                $em->flush();
                $this->addFlash('success','Le cours a été supprimé');
            }catch (\Exception $e){
                $this->addFlash('danger','Le cours n\'a pu être supprimé');
            }
        }
        return $this->redirectToRoute('course_list');
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






















