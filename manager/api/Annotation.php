<?php

/**
 * Holds informations about one annotation.
 *
 * @author Tobias Sarnowski
 */
interface Annotation {

    /**
     * The annotation's name without the @.
     *
     * @abstract
     * @return string
     */
    public function getName();

    /**
     * The annotations payload, may be empty.
     *
     * @abstract
     * @return string
     */
    public function getPayload();

}
