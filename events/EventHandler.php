<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
interface EventHandler {

    /**
     * @abstract
     * @param string $event
     * @param array $arguments
     * @return void
     */
    function fire($event, $arguments = array());

}
