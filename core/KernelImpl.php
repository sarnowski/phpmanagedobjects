<?php

// debug utility
require('debug.php');

// phpobjectproxy library
require('phpobjectproxy/src/ObjectProxyGenerator.php');

// api
require('api/DocParser.php');
require('api/Kernel.php');
require('api/KernelCallChain.php');
require('api/KernelException.php');

// spi
require('spi/KernelExtension.php');
require('spi/ClassAnalyzerExtension.php');
require('spi/ClassCallExtension.php');
require('spi/ClassConstructionExtension.php');

// implementation
require('KernelCallChainImpl.php');
require('KernelClassLoader.php');
require('KernelProxy.php');

/**
 *
 *
 * @author Tobias Sarnowski
 */
class KernelImpl implements Kernel {

    private static $SINGLETON;

    private $classLoader;

    private $classes = array();

    private $proxies = array();

    private $extensions = array();

    private $names = array();

    public function __construct() {
        self::$SINGLETON = $this;
        $this->classLoader = new KernelClassLoader();
    }

    public function getClassLoader() {
        return $this->classLoader;
    }

    function initialize() {
        // look for extension classes
        foreach ($this->classLoader->getLoadedClasses() as $className) {
            $class = $this->getClass($className);
            if (!$class->implementsInterface('KernelExtension')) continue;
            if ($class->isAbstract()) continue;
            if ($class->isInterface()) continue;
            $extension = new $className();
            $extension->setKernel($this);
            $this->extensions[] = $extension;
        }

        // load all proxies
        $proxies = array();
        foreach ($this->classLoader->getLoadedClasses() as $className) {
            $class = $this->getClass($className);

            if ($class->implementsInterface('KernelExtension')) continue;
            if ($class->isAbstract()) continue;
            if ($class->isInterface()) continue;

            $proxy = new KernelProxy($this, $class);
            $proxies[] = $proxy;

            $instance = ObjectProxyGenerator::generateObject($className, $proxy);
            $this->proxies[$className] = $instance;

            $this->registerName($className, $className);
        }

        foreach ($proxies as $proxy) {
            $proxy->onLoad();
        }
    }

    function getParents(ReflectionClass $class, $list = array()) {
        if ($class->getParentClass() == null) {
            return $list;
        }
        $list[] = $class->getParentClass();
        return $this->getParents($class, $list);
    }

    function registerName($name, $className) {
        if (!isset($this->names[$name])) {
            $this->names[$name] = array();
        }
        $this->names[$name][] = $className;
    }

    function getNames() {
        return array_keys($this->names);
    }

    function getExtensions() {
        return $this->extensions;
    }

    /**
     * @param  $className
     * @return ReflectionClass
     */
    function getClass($className) {
        if (!isset($this->classes[$className])) {
            $this->classes[$className] = new ReflectionClass($className);
        }
        return $this->classes[$className];
    }

    function getInstance($name) {
        if (!isset($this->names[$name])) {
            throw new KernelException("No class for $name registered.");
        }
        if (count($this->names[$name]) != 1) {
            throw new KernelException("Name is not unique.");
        }

        $className = $this->names[$name][0];
        if (!isset($this->proxies[$className])) {
            throw new KernelException("Implementation $className not found.");
        }

        return $this->proxies[$className];
    }

    function getInstances($name) {
        if (!isset($this->names[$name])) {
            return array();
        }

        $proxies = array();
        foreach ($this->names[$name] as $className) {
            if (!isset($this->proxies[$className])) {
                throw new KernelException("Implementation $className not found.");
            }
            $proxies[] = $this->proxies[$className];
        }
        return $proxies;
    }

    public static function getSingleton() {
        return self::$SINGLETON;
    }

    /**
     * Boots the kernel and initializes the system.
     *
     * @static
     * @param array|string $dirs
     * @return Kernel
     */
    public static function boot($dirs) {
        $kernel = new KernelImpl();
        if (is_array($dirs)) {
            foreach ($dirs as $dir) {
                $kernel->classLoader->addPath($dir);
            }
        } else {
            $kernel->classLoader->addPath($dirs);
        }

        $kernel->initialize();
        return $kernel;
    }

    function __toString() {
        return 'KernelImpl{}';
    }

}
