<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
interface ClassCallExtension extends KernelExtension {

    function processCall($call);

}
