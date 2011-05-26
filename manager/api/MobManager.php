<?php

/**
 * Provides basic access to the application mobs.
 *
 * @author Tobias Sarnowski
 */
interface MobManager {

    /**
     * Retrieves a mob by its name. If there is no mob or more than one mob, this will throw an exception.
     *
     * @abstract
     * @param string $name the mob's name
     * @return mixed the retrieved mob
     * @throws MobException if the mob is not found
     */
    public function getMob($name);

    /**
     * Retrieves all mobs by their name.
     *
     * @abstract
     * @param string $name the mob's name
     * @return array the retrieved mobs
     */
    public function getMobs($name);

    /**
     * Checks if a managed object exists in the manager.
     *
     * @abstract
     * @param string $name
     * @return bool
     */
    public function hasMob($name);

    /**
     * Registers a managed object in the manager.
     *
     * @abstract
     * @param ReflectionClass $class
     * @return void
     */
    public function registerMob(ReflectionClass $class);

    /**
     * Sets a name for a registered mob.
     *
     * @abstract
     * @param string $name
     * @param string $mobName
     * @return void
     */
    public function registerName($name, $mobName);

    /**
     * Inserts an instantiated object into the manager.
     *
     * @abstract
     * @param string $mobName
     * @param mixed $object
     * @return void
     */
    public function registerObject($mobName, $object);

    /**
     * A list of all registered extensions.
     *
     * @abstract
     * @return MobManagerExtension[]
     */
    public function getExtensions();

    /**
     * Get an extension by its concrete name.
     *
     * @abstract
     * @param string $name
     * @return MobManagerExtension
     * @throws MobException if extension does not exist
     */
    public function getExtension($name);

    /**
     * Checks if the extension exists.
     *
     * @abstract
     * @param string $name
     * @return boolean
     */
    public function hasExtension($name);

    /**
     * Registers an extension on the manager.
     *
     * @abstract
     * @param ReflectionClass $class
     * @return void
     */
    public function registerExtension(ReflectionClass $class);

    /**
     * Adds a directory for automatic discovery of mobs.
     *
     * @abstract
     * @param string $dir
     * @return void
     */
    public function addDirectory($dir);

    /**
     * Provides the implementation used for annotation parsing.
     *
     * @abstract
     * @return AnnotationParser
     */
    public function getAnnotationParser();

    /**
     * Starts the manager, extensions cannot get registered anymore at this point.
     *
     * @abstract
     * @return void
     */
    public function start();

    /**
     * Stops the manager.
     *
     * @abstract
     * @return void
     */
    public function stop();

}
