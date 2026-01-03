<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'attr' => ['class' => 'form-control']
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => 'App\Entity\CategorieVoiture',
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Sélectionner une catégorie',
                'attr' => ['class' => 'form-select']
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['class' => 'form-control']
            ])
            ->add('annee', IntegerType::class, [
                'label' => 'Année',
                'attr' => ['class' => 'form-control', 'min' => 1900, 'max' => 2100]
            ])
            ->add('prixParJour', MoneyType::class, [
                'label' => 'Prix par jour (MAD)',
                'currency' => 'MAD',
                'attr' => ['class' => 'form-control']
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'disponible',
                    'Louée' => 'louee',
                    'En maintenance' => 'maintenance',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('nombrePlaces', IntegerType::class, [
                'label' => 'Nombre de places',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 1]
            ])
            ->add('typeCarburant', TextType::class, [
                'label' => 'Type de carburant',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('photoPrincipale', UrlType::class, [
                'label' => 'URL de la photo principale',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
