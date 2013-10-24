<?php

namespace Siamosoci\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Registration extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text',array('label'=>'Name'));
        $builder->add('lastName', 'text',array('label'=>'Surname'));
        $builder->add('email', 'email');
        $builder->add('password', 'password');
        $builder->add('Sing Up','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Siamosoci\PlatformBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
?>
