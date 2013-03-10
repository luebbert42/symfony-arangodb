<?php
namespace Triagens\ArangodbBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TopicsToTopiclistTransformer implements DataTransformerInterface
{

    /**
     * transform app data > norm data, i.e. array2string
     * @param mixed $topiclist
     * @return array|mixed
     */
    public function transform($topicsArray)
    {
        if (!$topicsArray) {
            return "";
        }
        return implode(",",$topicsArray);

    }


    /**
     * transform norm data 2 app data, i.e. string2array
     * @param mixed $topicsArray
     * @return mixed|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($topicsString)
    {
        if (null === $topicsString) {
            return array();
        }

        return explode(",",$topicsString);
    }
}