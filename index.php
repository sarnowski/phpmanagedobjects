<?php
/**
 * Bootstraps the kernel.
 *
 * @author Tobias Sarnowski
 */

require('core/KernelImpl.php');

$kernel = KernelImpl::boot(dirname(__FILE__));

$obj = $kernel->getInstance('DependentObject');
echo "object name: ".$obj->getName()."<br/>";