<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AdType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label, $placeholder, $class = "") {
        return [
            'label' => $label,
            'attr'  => [
                'placeholder' => $placeholder,
                'class' => $class
            ]
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Tapez un super titre pour votre annonce"))
            ->add('coverImage', UrlType::class, $this->getConfiguration("URL de l'image", "Donnez l'adresse d'une image qui donne vraiment envie"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Donnez une description globale de l'annonce"))
            ->add('content', TextareaType::class, $this->getConfiguration("Description détaillée", "Tapez une description qui donne envie de passer nuit chez vous","tinymce"))            
            ->add('city', TextType::class, $this->getConfiguration("Ville", "Tapez la ville où se trouve votre logement"))
            ->add('address', TextareaType::class, $this->getConfiguration("Adresse complète", "Tapez une adresse précise et claire de votre appartement en location","tinymce"))
            ->add('rooms', IntegerType::class, $this->getConfiguration("Pièces", "Indiquez le nombre de pièces"))            
            ->add('bedrooms', IntegerType::class, $this->getConfiguration("Chambres", "Indiquez le nombre de chambre"))
            ->add('floor', IntegerType::class, $this->getConfiguration("Etage", "Indiquez le nombre de niveau d'étage"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix par nuit", "Indiquez le prix que vous voulez pour une nuit"))
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            ->add('images',
                CollectionType::class, [                
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false              
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
