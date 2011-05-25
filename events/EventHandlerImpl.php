<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class EventHandlerImpl implements EventHandler {

    /**
     * @inject extension EventExtension
     * @var EventExtension
     */
    var $eventExtension;

    /**
     * @inject
     * @var Kernel
     */
    var $kernel;

    /**
     * @param string $event
     * @param array $arguments
     * @return void
     */
    function fire($event, $arguments = null) {
        if ($arguments == null) {
            $arguments = array();
        }
        $events = $this->eventExtension->getEvents();
        if (!isset($events[$event])) {
            return;
        }
        foreach ($events[$event] as $observer) {
            $instance = $this->kernel->getInstance($observer['class']->getName());
            $observer['method']->invokeArgs($instance, $arguments);
        }
    }
}
