<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class SimpleObject implements SimpleInterface {

    function blub() {
        echo "bla";
    }

    function __toString() {
        return "SimpleObject{}";
    }
}
