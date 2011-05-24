<?php

require_once('phpobjectproxy/src/ObjectProxyGenerator.php');

require_once('debug.php');
require_once('Kernel.php');
require_once('KernelProxy.php');
require_once('KernelException.php');
require_once('DocParser.php');
require_once('ClassLoaderImpl.php');


/**
 *
 *
 * @author Tobias Sarnowski
 */
class KernelImpl implements Kernel {

    private static $SINGLETON;

    private $classLoader;

    private $classes = array();

    private $instances = array();

    private $extensions = array();

    private $names = array();

    public function __construct() {
        self::$SINGLETON = $this;
        $this->classLoader = new ClassLoaderImpl();
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

            $proxy = new KernelProxy($this);
            $instance = ObjectProxyGenerator::generateObject($className, $proxy);
            $proxies[] = $proxy;

            $this->registerName($className, $instance);
        }

        foreach ($proxies as $proxy) {
            $proxy->onLoad($class);
        }
    }

    function getParents($class, $list = array()) {
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
        if (!isset($this->instances[$className])) {

            $this->instances[$className] = $instance;
        }
        return $this->instances[$className];
    }

    public static function getSingleton() {
        return self::$SINGLETON;
    }

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
}
