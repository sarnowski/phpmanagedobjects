<?php

/**
 * Enlists every method which wants to be invoked upon an event.
 *
 * @author Tobias Sarnowski
 */
class EventExtension implements ClassAnalyzerExtension {

    /**
     * @var MobManager
     */
    private $mobManager;

    /**
     * @var array
     */
    private $events = array();

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
    function analyzeClass($name, ReflectionClass $class) {
        foreach ($class->getMethods() as $method) {
            $methodAnnotations = $this->mobManager->getAnnotationParser()->getAnnotatedMethod($method);
            foreach ($methodAnnotations->getAnnotations() as $annotation) {
                if ($annotation->getName() == 'observes') {
                    $event = $annotation->getPayload();

                    if (!isset($this->events[$event])) {
                        $this->events[$event] = array();
                    }

                    $this->events[$event][] = array(
                        'name' => $name,
                        'method' => $method
                    );
                }
            }
        }
    }

    function getEvents() {
        return $this->events;
    }
}
