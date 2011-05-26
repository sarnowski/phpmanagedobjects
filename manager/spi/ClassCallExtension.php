<?php

/**
 * Called on every mob method invocation.
 *
 * @author Tobias Sarnowski
 */
interface ClassCallExtension extends MobManagerExtension {

    /**
     * Use the chain to proceed with the invocation and return the chain's result
     * or your own.
     *
     * @abstract
     * @param MobCallChain $chain
     * @return mixed
     */
    public function processCall(MobCallChain $chain);

}
