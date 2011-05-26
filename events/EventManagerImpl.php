<?php

/**
 * Implements the EventManager interface.
 *
 * @name eventManager
 * @author Tobias Sarnowski
 */
class EventManagerImpl implements EventManager {

    /**
     * @inject extension EventExtension
     * @var EventExtension
     */
    var $eventExtension;

    /**
     * @inject
     * @var MobManager
     */
    var $mobManager;

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
            $instance = $this->mobManager->getMob($observer['name']);
            $observer['method']->invokeArgs($instance, $arguments);
        }
    }
}
