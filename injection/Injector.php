<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class Injector implements ClassAnalyzerExtension, ClassConstructionExtension {

    /**
     * @var KernelImpl
     */
    private $kernel;

    function setKernel($kernel) {
        $this->kernel = $kernel;
    }

    /**
     * @param ReflectionClass $class
     * @return void
     */
    function analyzeClass($class) {
        // register all interface names
        foreach ($class->getInterfaceNames() as $interfaceName) {
            debug("registering $interfaceName");
            $this->kernel->registerName($interfaceName, $class->getName());
        }

        // register all parent names
        foreach ($this->getParentNames($class) as $parentName) {
            debug("registering $parentName");
            $this->kernel->registerName($parentName, $class->getName());
        }

        // use @name annotation for registering
        $name = DocParser::parseAnnotation($class->getDocComment(), 'name');
        if ($name) {
            debug("registering $name");
            $this->kernel->registerName($name, $class->getName());
        }
    }

    /**
     * Creates a flat list of all names of the parents.
     *
     * @param ReflectionClass $class
     * @param array $parents
     * @return array
     */
    function getParentNames(ReflectionClass $class, $parents = array()) {
        $parent = $class->getParentClass();
        if (!$parent) return $parents;

        if ($parent->getParentClass()) {
            $parents = $this->getParentNames($parent, $parents);
        }

        $parents[] = $parent;
        return $parents;
    }

    function constructClass($instance, $class) {
        // inject all dependencies
        // TODO implement
        debug("injecting dependencies");
    }

}
