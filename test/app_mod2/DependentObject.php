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
    var $simpleObject;


    public function getName() {
        return "DependentObject{".$this->simpleObject."}";
    }

    function __toString() {
        return "{DependentObject}";
    }


}
