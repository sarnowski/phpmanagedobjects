<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class KernelClassLoader {

    private $classPaths = array();
    private $loadedClasses = array();

    public function addPath($dir) {
        $this->classPaths = $this->search_classes_r($dir, $this->classPaths);
        foreach ($this->classPaths as $className => $classPath) {
            $this->loadClass($className);
        }
    }

    private function search_classes_r($dir, $classes) {
        if (realpath($dir) == realpath(dirname(__FILE__))) {
            return $classes;
        }

        $dh = dir($dir);
        while (false !== ($entry = $dh->read())) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $path = "$dir/$entry";

            if (is_file($path)) {
                if (substr($entry, strlen($entry) - strlen(".php")) != '.php') {
                    continue; // only php files
                }
                if (substr($entry, 0, 1) != strtoupper(substr($entry, 0, 1))) {
                    continue; // only uppercase classes
                }
                $className = substr($entry, 0, strlen($entry) - strlen(".php"));
                $classes[$className] = $path;
            } elseif (is_dir($path)) {
                $classes = $this->search_classes_r($path, $classes);
            }
        }
        $dh->close();
        return $classes;
    }

    public function loadClass($className) {
        if (in_array($className, $this->loadedClasses)) {
            return;
        }
        if (!isset($this->classPaths[$className])) {
            throw new KernelException("Cannot load class $className automatically.");
        }
        $file = $this->classPaths[$className];

        require($file);
        $this->loadedClasses[] = $className;
    }

    public function getLoadedClasses() {
        return $this->loadedClasses;
    }
}

function __autoload($className) {
    KernelImpl::getSingleton()->getClassLoader()->loadClass($className);
}