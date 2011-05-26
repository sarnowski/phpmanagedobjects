<?php

/**
 * Abstract interface used by all extension interfaces.
 *
 * @author Tobias Sarnowski
 */
interface MobManagerExtension {

    /**
     * Will be invoked before any other extension method will be called.
     *
     * @abstract
     * @param MobManager $mobManager
     * @return void
     */
    public function setMobManager(MobManager $mobManager);

}
