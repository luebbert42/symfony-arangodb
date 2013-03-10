<?php

namespace Triagens\ArangodbBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class EditmovieType extends AddmovieType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('id', 'hidden');
    }

    public function getName()
    {
        return 'editmovie';
    }
}
