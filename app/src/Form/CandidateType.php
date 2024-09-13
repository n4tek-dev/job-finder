<?php

namespace App\Form;

use App\Entity\Candidates;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class CandidateType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Build the form for candidate entity.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => new NotBlank(),
                'label' => $this->translator->trans('First Name')
            ])
            ->add('lastName', TextType::class, [
                'constraints' => new NotBlank(),
                'label' => $this->translator->trans('Last Name')
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
                'label' => $this->translator->trans('Email Address')
            ])
            ->add('phoneNumber', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\+?[0-9]{9,15}$/',
                        'message' => $this->translator->trans('Please enter a valid phone number.'),
                    ])
                ],
                'label' => $this->translator->trans('Phone Number')
            ]);

        if ($options['include_password']) {
            $builder->add('password', PasswordType::class, [
                'constraints' => new NotBlank(),
                'label' => $this->translator->trans('Password')
            ]);
        }
    }

    /**
     * Configure the options for this form type.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidates::class,
            'include_password' => true,
        ]);
    }
}