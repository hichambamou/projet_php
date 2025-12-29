<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Client;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ReservationType extends AbstractType
{
    public function __construct(private ?Security $security = null)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security?->getUser();
        $isAdmin = $user && in_array('ROLE_ADMIN', $user->getRoles());

        $builder
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant (MAD)',
                'currency' => 'MAD',
                'attr' => ['class' => 'form-control']
            ]);

        if ($isAdmin) {
            $builder
                ->add('statut', ChoiceType::class, [
                    'label' => 'Statut',
                    'choices' => [
                        'En attente' => 'en_attente',
                        'Confirmée' => 'confirmee',
                        'Annulée' => 'annulee',
                    ],
                    'attr' => ['class' => 'form-select']
                ])
                ->add('client', EntityType::class, [
                    'class' => Client::class,
                    'choice_label' => 'nom',
                    'attr' => ['class' => 'form-select']
                ]);
        }

        $builder->add('voiture', EntityType::class, [
            'class' => Voiture::class,
            'choice_label' => function(Voiture $voiture) {
                return $voiture->getMarque() . ' ' . $voiture->getModele() . ' (' . $voiture->getAnnee() . ')';
            },
            'query_builder' => function ($er) {
                return $er->createQueryBuilder('v')
                    ->where('v.statut = :statut')
                    ->setParameter('statut', 'disponible');
            },
            'attr' => ['class' => 'form-select']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
