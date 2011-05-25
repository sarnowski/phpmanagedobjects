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
            $this->kernel->registerName($interfaceName, $class->getName());
        }

        // register all parent names
        foreach ($this->getParentNames($class) as $parentName) {
            $this->kernel->registerName($parentName, $class->getName());
        }

        // use @name annotation for registering
        $name = DocParser::parseAnnotation($class->getDocComment(), 'name');
        if ($name) {
            $this->kernel->registerName($name, $class->getName());
        }

        // register the name itself without capitalization
        $name = strtolower(substr($class->getName(), 0, 1)).substr($class->getName(), 1);
        $this->kernel->registerName($name, $class->getName());
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

    /**
     * @param mixed $instance
     * @param ReflectionClass $class
     * @return void
     */
    function constructClass($instance, $class) {
        // inject all dependencies
        foreach ($class->getProperties() as $property) {
            $inject = DocParser::parseAnnotation($property->getDocComment(), 'inject');
            if ($inject !== null) {
                $listInjection = false;
                $optional = false;

                $name = $property->getName();
                if (!empty($inject)) {
                    $inject = explode(' ', $inject);
                    if ($inject[0] == 'optional') {
                        $optional = true;
                        array_shift($inject);
                    }
                    if ($inject[0] == 'array') {
                        $listInjection = true;
                        array_shift($inject);
                    }
                    if (count($inject) > 0) {
                        $name = $inject[0];
                    }
                }
                if ($optional) {
                    if (!$this->kernel->hasInstances($name)) {
                        continue;
                    }
                }
                if ($listInjection) {
                    $dependency = $this->kernel->getInstances($name);
                } else {
                    $dependency = $this->kernel->getInstance($name);
                }
                $property->setValue($instance, $dependency);
            }
        }
    }

    function __toString() {
        return 'Injector{}';
    }
}
