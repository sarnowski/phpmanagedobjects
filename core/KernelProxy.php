<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class KernelProxy implements ObjectProxy {

    /**
     * @var KernelImpl
     */
    private $kernel;

    /**
     * @var ReflectionClass
     */
    private $class;

    function __construct(KernelImpl $kernel, ReflectionClass $class) {
        $this->kernel = $kernel;
        $this->class = $class;
    }

    function onLoad() {
        foreach ($this->kernel->getExtensions() as $extension) {
            if ($extension instanceof ClassAnalyzerExtension) {
                $extension->analyzeClass($class);
            }
        }
    }

    /**
     * Will be called for object creation.
     *
     * @param object $instance the object instance
     * @return object
     */
    function onConstruct($instance) {
        foreach ($this->kernel->getExtensions() as $extension) {
            if ($extension instanceof ClassConstructionExtension) {
                $extension->constructClass($instance, $this->class);
            }
        }
    }

    /**
     * Will be called on a method call.
     *
     * @param ObjectProxyCall $call the method call
     * @return mixed
     */
    function onCall(ObjectProxyCall $call) {
        foreach ($this->kernel->getExtensions() as $extension) {
            if ($extension instanceof ClassCallExtension) {
                $extension->processCall($call);
            }
        }
        return $call->call();
    }
}
