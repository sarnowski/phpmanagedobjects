<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class KernelCallChainImpl implements KernelCallChain {

    /**
     * @var ReflectionMethod
     */
    private $method;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $extensions;

    function __construct(ObjectProxyCall $call, $extensions) {
        $this->call = $call;
        $this->method = $call->getMethod();
        $this->parameters = $call->getArguments();
        $this->extensions = $extensions;
    }

    function getDelegate() {
        return $this->call->getInstance();
    }

    function getMethod() {
        return $this->method;
    }

    function getParameters() {
        return $this->parameters;
    }

    function proceed() {
        if (count($this->extensions) == 0) {
            return $this->method->invokeArgs($this->getDelegate(), $this->parameters);
        } else {
            $extension = array_shift($this->extensions);
            return $extension->processCall($this);
        }
    }

    function setMethod(&$method) {
        $this->method = $method;
    }

    function setParameters(&$parameters) {
        $this->method = $parameters;
    }

    function __toString() {
        return 'KernelCallChainImpl{}';
    }


}
