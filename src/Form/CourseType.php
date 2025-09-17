<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Trainer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label'=>'Titre'
            ])
            ->add('content',TextareaType::class,[
                'label'=>'Description',
                'required'=>false
            ])
            ->add('duration',IntegerType::class,[
                'label'=>'Durée (jours)'
            ])
            ->add('category',EntityType::class,[
                'label'=>'Catégorie',
                'class'=>Category::class,
                'choice_label'=>'name',
                'placeholder'=>'-- Choisir une catégorie --'
            ])
            ->add('trainers',EntityType::class,[
                'label'=>'Formateurs',
                'class'=>Trainer::class,
                'choice_label'=>'fullname',
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.lastname','ASC')
                        ->addOrderBy('f.firstname','ASC');
                },
                'placeholder'=>'-- Choisir un formateur --',
                'multiple'=>true,
            ])
            //1er option pour créer le bouton submit du formulaire
//            ->add('btnCreate',SubmitType::class,[
//                'label'=>'Ajouter'
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
