<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class Weaver implements ClassAnalyzerExtension, ClassCallExtension {

    /**
     * @var Kernel
     */
    private $kernel;

    function setKernel($kernel) {
        $this->kernel = $kernel;
    }

    function analyzeClass($class) {
        // analyze class and find interceptors
        // TODO implement
        debug("looking for interceptors");
    }

    function processCall(KernelCallChain $chain) {
        // on first call, look for interceptors
        // call interceptors
        // TODO implement
        debug("calling method");
        return $chain->proceed();
    }


}
