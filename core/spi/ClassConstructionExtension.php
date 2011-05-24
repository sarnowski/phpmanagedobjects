<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
interface ClassConstructionExtension extends KernelExtension {

    function constructClass($instance, $class);

}
