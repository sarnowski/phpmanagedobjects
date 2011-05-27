<?php

/**
 * MobManager proxy to provide the extension functionalities.
 *
 * @author Tobias Sarnowski
 */
class MobProxyImpl implements ObjectProxy {

    /**
     * @var MobManagerImpl
     */
    private $mobManager;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ReflectionClass
     */
    private $class;

    function __construct(MobManagerImpl $mobManager, $name) {
        $this->mobManager = $mobManager;
        $this->name = $name;
    }

    function setClass(ReflectionClass $class) {
        $this->class = $class;
    }

    function onLoad() {
        foreach ($this->mobManager->getExtensions() as $extension) {
            if ($extension instanceof ClassAnalyzerExtension) {
                $extension->analyzeClass($this->name, $this->class);
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
        foreach ($this->mobManager->getExtensions() as $extension) {
            if ($extension instanceof ClassConstructionExtension) {
                $extension->constructClass($instance, $this->name, $this->class);
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
        $extensions = array();
        foreach ($this->mobManager->getExtensions() as $extension) {
            if ($extension instanceof ClassCallExtension) {
                $extensions[] = $extension;
            }
        }
        $chain = new MobManagerCallChainImpl($this->name, $call, $extensions);
        return $chain->proceed();
    }

    function __toString() {
        return 'MobProxyImpl';
    }
}
