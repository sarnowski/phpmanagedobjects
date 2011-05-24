<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
interface KernelCallChain {

    function proceed();

    function getDelegate();

    function getMethod();

    function setMethod(&$method);

    function getParameters();

    function setParameters(&$parameters);

}
