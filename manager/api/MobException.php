<?php

/**
 * Indicates an error within the MobManager.
 *
 * @author Tobias Sarnowski
 */
class MobException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}
