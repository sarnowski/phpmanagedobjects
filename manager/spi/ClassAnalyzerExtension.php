<?php

/**
 * Extension interface to provide additional analyzis functionality.
 *
 * @author Tobias Sarnowski
 */
interface ClassAnalyzerExtension extends MobManagerExtension {

    /**
     * Called to analyze a loaded managed object class.
     *
     * @abstract
     * @param string $name
     * @param ReflectionClass $class
     * @return void
     */
    public function analyzeClass($name, ReflectionClass $class);

}
