<?php

/**
 * Provides utility methods to parse annotations from classes and methods.
 *
 * @author Tobias Sarnowski
 */
interface AnnotationParser {

    /**
     * Provides an informal object which holds informations about annotations.
     *
     * @abstract
     * @param ReflectionClass $class
     * @return AnnotatedDefinition
     */
    public function getAnnotatedClass(ReflectionClass $class);

    /**
     * Provides an informal object which holds informations about annotations.
     *
     * @abstract
     * @param ReflectionMethod $method
     * @return AnnotatedDefinition
     */
    public function getAnnotatedMethod(ReflectionMethod $method);

    /**
     * Provides an informal object which holds informations about annotations.
     *
     * @abstract
     * @param ReflectionProperty $property
     * @return AnnotatedDefinition
     */
    public function getAnnotatedProperty(ReflectionProperty $property);

}
