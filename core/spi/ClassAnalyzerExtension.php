<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
interface ClassAnalyzerExtension extends KernelExtension {

    function analyzeClass($class);

}
