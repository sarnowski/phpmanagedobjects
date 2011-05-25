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
     * @observes kernel.booted
     * @return void
     */
    function triggeredOnBoot() {
        echo "The kernel booted!<br/>";
    }

    function __toString() {
        return "SimpleObject{}";
    }
}
