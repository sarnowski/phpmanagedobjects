<?php

/**
 * Provides the possibility to fire events.
 *
 * @author Tobias Sarnowski
 */
interface EventManager {

    /**
     * @abstract
     * @param string $event
     * @param array $arguments
     * @return void
     */
    function fire($event, $arguments = array());

}
