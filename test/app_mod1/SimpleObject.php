<?php

/**
 *
 *
 * @author Tobias Sarnowski
 * @name bestObject
 */
class SimpleObject implements SimpleInterface {

    function blub() {
        echo "bla";
    }

    /**
     * @observes manager.started
     * @return void
     */
    function triggeredOnBoot() {
        echo "The manager started!<br/>";
    }

    function __toString() {
        return "SimpleObject{}";
    }
}
