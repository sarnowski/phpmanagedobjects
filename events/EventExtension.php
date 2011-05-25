<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class EventExtension implements ClassAnalyzerExtension {

    /**
     * @var Kernel
     */
    private $kernel;

    private $events = array();

    function setKernel($kernel) {
        $this->kernel = $kernel;
    }

    /**
     * @param ReflectionClass $class
     * @return void
     */
    function analyzeClass($class) {
        foreach ($class->getMethods() as $method) {
            $observes = DocParser::parseAnnotations($method->getDocComment(), 'observes');
            foreach ($observes as $event) {
                if (!isset($this->events[$event])) {
                    $this->events[$event] = array();
                }
                $this->events[$event][] = array(
                    'class' => $class,
                    'method' => $method
                );
            }
        }
    }

    function getEvents() {
        return $this->events;
    }
}
