<?php

/**
 * Provides informations about some kind of definition (method, class, ...)
 *
 * @author Tobias Sarnowski
 */
interface AnnotatedDefinition {

    /**
     * Provides a list of all annotations.
     *
     * @abstract
     * @return Annotation[]
     */
    public function getAnnotations();

    /**
     * Returns the annotation with the given names. Throws an exception if
     * the annotation was not found or is present more than one time.
     *
     * @abstract
     * @param string $name
     * @return Annotation
     */
    public function getAnnotation($name);

    /**
     * Returns if the annotation is present.
     *
     * @abstract
     * @param string $name
     * @return boolean
     */
    public function hasAnnotation($name);

    /**
     * Returns if the annotation is present one time.
     *
     * @abstract
     * @param string $name
     * @return boolean
     */
    public function hasSingleAnnotation($name);

}
