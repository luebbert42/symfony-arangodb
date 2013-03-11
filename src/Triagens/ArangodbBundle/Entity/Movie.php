<?php

namespace Triagens\ArangodbBundle\Entity;
use triagens\ArangoDb\Document;

class Movie extends Document
{

    protected $_internal = array("id");

    /**
     * assign each value of $values to movie
     * @param $values
     * @throws \InvalidArgumentException
     */
    public function populate($values) {

        if (!is_array($values)) {
            throw new \InvalidArgumentException("values is expected to be an array");
        }
        foreach ($values as $key => $value) {
            if (in_array($key,$this->_internal)) continue;
            $this->set($key,$value);
        }
    }
}