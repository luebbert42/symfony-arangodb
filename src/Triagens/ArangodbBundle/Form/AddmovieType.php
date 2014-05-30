<?php

namespace Triagens\ArangodbBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Triagens\ArangodbBundle\Form\DataTransformer\TopicsToTopiclistTransformer;

class AddmovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $transformer = new TopicsToTopiclistTransformer();

        $builder->add('title', 'text', array(
                "label" => "Title:",
                "required" => true,
                "attr" => array('class' => 'input-xlarge')
            )
        );

        $builder->add('genre', 'choice', array(
                "label" => "Genre:",
                "empty_value" => "Choose an option",
                "required" => true,
                "choices" => array("sci-fi" => "sci-fi", "trash" => "trash"),
                "attr" => array('class' => 'input-xlarge')
            )
        );


        $builder->add('released', 'integer', array(
                "label" => "Released in year:",
                "required" => true,
                "attr" => array('class' => 'input-xlarge')
            )
        );

        $builder->add(
            $builder->create('topics', 'text', array(
                "label" => "Topics (please separate by \",\"):",
                "required" => true,
                "attr" => array('class' => 'input-xlarge')
                ))
                ->addModelTransformer($transformer)
        );
    }

    public function getName()
    {
        return 'addmovie';
    }
}
