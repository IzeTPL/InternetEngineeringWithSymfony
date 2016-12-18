<?php

namespace AppBundle\Form;

use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder
		    ->add('title', null, [
			    'attr' => ['autofocus' => true],
			    'label' => 'label.title',
		    ])
		    ->add('summary', TextareaType::class, [
			    'label' => 'label.summary',
		    ])
		    ->add('content', null, [
			    'attr' => ['rows' => 20, 'class' => 'tinymce'],
			    'label' => 'label.content',
		    ])
		    ->add('author', null, [
		    	'disabled' => true,
			    'label' => 'label.author',
		    ])
		    ->add('publishedAt', DateTimePickerType::class, [
		    	'disabled' => true,
			    'label' => 'label.published_at',
		    ])
	    ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_post';
    }


}
