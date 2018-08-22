<?php

namespace GM\VideothequeBundle\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use GM\VideothequeBundle\Entity\Categorie;

use Doctrine\ORM\EntityRepository;

class FilmType extends AbstractType
{
    protected $businessLogicOptions;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->businessLogicOptions['owner_user_id'] = $options['owner_user_id'];
        $builder->add('titre')->add('description')
        ->add('date_sortie', DateType::class, array(
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
        ))
        ->add('categorie', EntityType::class, array(
            'class' => Categorie::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                          ->where("c.owner = :owner_user_id")
                          ->setParameter('owner_user_id', $this->businessLogicOptions['owner_user_id'])
                          ->orderBy('c.nom', 'ASC');
            },
                        
            'choice_label' => function ($category) {
                if ($category->isOwner($this->businessLogicOptions['owner_user_id'])){
                    return $category->getNom();
                }
            },
        ))
        ;
        
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GM\VideothequeBundle\Entity\Film',
            'owner_user_id' => null // In the future put by default the main admin User ID
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gm_videothequebundle_film';
    }


}
