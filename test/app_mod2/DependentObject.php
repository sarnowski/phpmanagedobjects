<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class DependentObject {

    /**
     * @inject
     * @var SimpleInterface
     */
    var $bestObject;


    /**
     * @return string
     */
    public function getName() {
        return "DependentObject{".$this->bestObject."}";
    }

    function __toString() {
        return "{DependentObject}";
    }

}
