<?php

/**
 * Holds informations about one annotation.
 *
 * @author Tobias Sarnowski
 */
class AnnotationImpl implements Annotation {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $payload;

    function __construct($name, $payload) {
        $this->name = $name;
        $this->payload = trim($payload);
    }

    /**
     * The annotation's name without the @.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * The annotations payload, may be empty.
     *
     * @return string
     */
    public function getPayload() {
        return $this->payload;
    }

    function __toString() {
        return 'AnnotationImpl';
    }
}
