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
     * @inject optional
     */
    var $test;


    /**
     * @return string
     */
    public function getName() {
        return "DependentObject{".$this->bestObject."}";
    }

    /**
     * @observes kernel.booted
     * @return void
     */
    public function greet() {
        echo "Greeting from ".$this->getName()."!<br/>";
    }

    function __toString() {
        return "{DependentObject}";
    }

}
