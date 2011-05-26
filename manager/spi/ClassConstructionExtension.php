<?php

/**
 * Triggered after instantiation of a mob.
 *
 * @author Tobias Sarnowski
 */
interface ClassConstructionExtension extends MobManagerExtension {

    /**
     * Will be called after instantiation and before the first method invocation on the object.
     *
     * @abstract
     * @param mixed $instance
     * @param string $name
     * @param ReflectionClass $class
     * @return void
     */
    public function constructClass($instance, $name, ReflectionClass $class);

}
