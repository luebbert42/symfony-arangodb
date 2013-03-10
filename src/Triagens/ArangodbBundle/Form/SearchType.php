<?php

namespace Triagens\ArangodbBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('search', 'text', array(
                "label" => "Search for topic:",
                "attr" => array('class' => 'input-xlarge')
            )
        );
    }

    public function getName()
    {
        return 'searchtopic';
    }
}
