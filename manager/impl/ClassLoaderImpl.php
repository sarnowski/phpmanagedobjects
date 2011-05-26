<?php

/**
 * Loads classes recursively.
 *
 * @author Tobias Sarnowski
 */
class ClassLoaderImpl {

    /**
     * @var array
     */
    private $classPaths = array();

    /**
     * @var ReflectionClass[]
     */
    private $classes = array();

    /**
     * Returns and caches reflection classes.
     *
     * @param string $className
     * @return ReflectionClass
     */
    public function getReflectionClass($className) {
        if (!isset($this->classes[$className])) {
            $this->classes[$className] = new ReflectionClass($className);
        }
        return $this->classes[$className];
    }

    /**
     * Loads all classes recursively and returns a list of all loaded classes.
     *
     * @param string $dir
     * @return array
     */
    public function addPath($dir) {
        $classes = $this->search_classes_r($dir, array());

        $this->classPaths = array_merge($this->classPaths, $classes);

        $refClasses = array();
        foreach ($classes as $className => $classPath) {
            $this->loadClass($className);
            $refClasses[] = $this->getReflectionClass($className);
        }

        return $refClasses;
    }

    /**
     * Creates a list of classnames corresponding to their source files.
     *
     * @param string $dir
     * @param array $classes
     * @return array
     */
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

    /**
     * @throws MobException
     * @param string $className
     * @return loads a class
     */
    public function loadClass($className) {
        if (class_exists($className) || interface_exists($className)) {
            return;
        }

        if (!isset($this->classPaths[$className])) {
            throw new MobException("Cannot load class $className automatically.");
        }

        require($this->classPaths[$className]);
    }

    function __toString() {
        return 'ClassLoaderImpl';
    }
}

function __autoload($className) {
    MobManagerImpl::getSingleton()->getClassLoader()->loadClass($className);
}