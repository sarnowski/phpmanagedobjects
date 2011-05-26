<?php

/**
 * A call chain used for ClassCallExtensions.
 *
 * @author Tobias Sarnowski
 */
interface MobCallChain {

    /**
     * Proceeds with the call and returns its result.
     *
     * @abstract
     * @return mixed
     */
    public function proceed();

    /**
     * The managed object's name.
     *
     * @abstract
     * @return string
     */
    public function getName();

    /**
     * The object the call was performed on.
     *
     * @abstract
     * @return mixed
     */
    public function getManagedObject();

    /**
     * The method which was invoked.
     *
     * @abstract
     * @return ReflectionMethod
     */
    public function getMethod();

    /**
     * The arguments used for the method invocation.
     *
     * @abstract
     * @return array
     */
    public function getParameters();

}
