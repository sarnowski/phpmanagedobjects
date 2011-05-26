<?php

/**
 * An invocation chain.
 *
 * @author Tobias Sarnowski
 */
class MobManagerCallChainImpl implements MobCallChain {

    /**
     * @var string
     */
    private $name;

    /**
     * @var ObjectProxyCall
     */
    private $call;

    /**
     * @var ClassCallExtension[]
     */
    private $extensions;

    function __construct($name, ObjectProxyCall $call, $extensions) {
        $this->name = $name;
        $this->call = $call;
        $this->extensions = $extensions;
    }

    /**
     * The managed object's name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * The object the call was performed on.
     *
     * @return mixed
     */
    public function getManagedObject() {
        return $this->call->getInstance();
    }

    function getMethod() {
        return $this->call->getMethod();
    }

    function getParameters() {
        return $this->call->getArguments();
    }

    function proceed() {
        if (count($this->extensions) == 0) {
            return $this->getMethod()->invokeArgs($this->getManagedObject(), $this->getParameters());
        } else {
            $extension = array_shift($this->extensions);
            return $extension->processCall($this);
        }
    }

    function __toString() {
        return 'MobManagerCallChainImpl';
    }
}
