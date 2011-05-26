<?php

/**
 * Provides dependency injection for all managed objects.
 *
 * @author Tobias Sarnowski
 */
class Injector implements ClassAnalyzerExtension, ClassConstructionExtension {

    /**
     * @var MobManager
     */
    private $mobManager;

    /**
     * Will be invoked before any other extension method will be called.
     *
     * @param MobManager $mobManager
     * @return void
     */
    public function setMobManager(MobManager $mobManager) {
        $this->mobManager = $mobManager;
    }

    /**
     * @param string $name
     * @param ReflectionClass $class
     * @return void
     */
    public function analyzeClass($name, ReflectionClass $class) {
        // register all interface names
        foreach ($class->getInterfaceNames() as $interfaceName) {
            $this->mobManager->registerName($interfaceName, $name);
        }

        // register all parent names
        foreach ($this->getParentNames($class) as $parentName) {
            $this->mobManager->registerName($parentName, $name);
        }

        // register the class name
        $this->mobManager->registerName($class->getName(), $name);
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
     * @param string $name
     * @param ReflectionClass $class
     * @return void
     */
    public function constructClass($instance, $name, ReflectionClass $class) {
        // inject all dependencies
        foreach ($class->getProperties() as $property) {
            $methodAnnotations = $this->mobManager->getAnnotationParser()->getAnnotatedProperty($property);
            if ($methodAnnotations->hasAnnotation('inject')) {
                $inject = $methodAnnotations->getAnnotation('inject')->getPayload();

                $listInjection = false;
                $extension = false;
                $optional = false;

                $name = $property->getName();
                if (!empty($inject)) {
                    $inject = explode(' ', $inject);
                    if ($inject[0] == 'optional') {
                        $optional = true;
                        array_shift($inject);
                    }
                    if ($inject[0] == 'extension') {
                        $extension = true;
                        array_shift($inject);
                    }
                    if ($inject[0] == 'array') {
                        if ($extension) {
                            throw new MobException("Cannot inject array of extensions.");
                        }
                        $listInjection = true;
                        array_shift($inject);
                    }
                    if (count($inject) > 0) {
                        $name = $inject[0];
                    }
                }
                if ($optional) {
                    if (!$this->mobManager->hasMob($name)) {
                        continue;
                    }
                }
                if ($extension) {
                    $dependency = $this->mobManager->getExtension($name);
                } elseif ($listInjection) {
                    $dependency = $this->mobManager->getMobs($name);
                } else {
                    $dependency = $this->mobManager->getMob($name);
                }
                $property->setValue($instance, $dependency);
            }
        }
    }

    function __toString() {
        return 'Injector{}';
    }
}
