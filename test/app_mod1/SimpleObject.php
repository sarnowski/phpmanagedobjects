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

    function __toString() {
        return "SimpleObject{}";
    }
}
