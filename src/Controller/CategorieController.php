<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/categories', name: 'category_')]
final class CategorieController extends AbstractController
{
    #[Route('/{id}/supprimer', name: 'delete',methods: ['GET'])]
    public function delete(Category $category,Request $request,EntityManagerInterface $em): Response
    {
     try{
         //1er solution pour supprimer les catégories
         /*if(count($category->getCourses())>0){
             //Si la catégorie possède des cours, on les supprime
             foreach ($category->getCourses() as $course){
                 $category->removeCourse($course);
             }
         }*/
         $em->remove($category);
         $em->flush();
         $this->addFlash('success','La catégorie a été supprimée !');
     }catch (\Exception $e){
         $this->addFlash('danger','La catégorie n\'a pas pu être supprimée <br>'.$e->getMessage());
     }
     return $this->redirectToRoute('cours_list');
    }
}
