<?php
/**
 * Bootstraps the kernel.
 *
 * @author Tobias Sarnowski
 */

require('debug.php');
require('manager/impl/MobManagerImpl.php');

$manager = MobManagerImpl::autostart(dirname(__FILE__));

$obj = $manager->getMob('dependentObject');
echo "object name: ".$obj->getName()."<br/>";

$manager->stop();