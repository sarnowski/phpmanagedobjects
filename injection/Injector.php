<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class Injector implements ClassAnalyzerExtension, ClassConstructionExtension {

    /**
     * @var Kernel
     */
    private $kernel;

    function setKernel($kernel) {
        $this->kernel = $kernel;
    }

    function analyzeClass($class) {
        // register class for various names
        // TODO implement
        debug("registering names");
    }

    function constructClass($instance, $class) {
        // inject all dependencies
        // TODO implement
        debug("injecting dependencies");
    }

}
