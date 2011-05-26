<?php

// phpobjectproxy library
require('proxy/src/ObjectProxyGenerator.php');

// api
require('../api/AnnotatedDefinition.php');
require('../api/Annotation.php');
require('../api/AnnotationParser.php');
require('../api/MobCallChain.php');
require('../api/MobException.php');
require('../api/MobManager.php');

// spi
require('../spi/MobManagerExtension.php');
require('../spi/ClassAnalyzerExtension.php');
require('../spi/ClassCallExtension.php');
require('../spi/ClassConstructionExtension.php');

// implementation
require('AnnotatedDefinitionImpl.php');
require('AnnotationImpl.php');
require('AnnotationParserImpl.php');
require('ClassLoaderImpl.php');
require('MobManagerCallChainImpl.php');
require('MobProxyImpl.php');

/**
 * Implementation for the MobManager interface.
 *
 * @author Tobias Sarnowski
 */
class MobManagerImpl implements MobManager {

    // for a singleton access :-/
    private static $SINGLETON;

    /**
     * @var ClassLoaderImpl
     */
    private $classLoader;

    /**
     * @var AnnotationParser
     */
    private $annotationParser;

    /**
     * @var bool
     */
    private $started = false;

    /**
     * @var bool
     */
    private $stopped = false;

    /**
     * @var array managed objects
     */
    private $mobs = array();

    /**
     * @var array managed objects classes
     */
    private $mobClasses = array();

    /**
     * @var array given objects
     */
    private $objects = array();

    /**
     * @var array aliases
     */
    private $names = array();

    /**
     * @var array extensions
     */
    private $extensions = array();

    public function __construct() {
        self::$SINGLETON = $this;

        $this->annotationParser = new AnnotationParserImpl();
        $this->classLoader = new ClassLoaderImpl();

        // register myself
        $this->registerObject('mobManagerImpl', $this);
        $this->registerName('mobManagerImpl', 'mobManagerImpl');
        $this->registerName('mobManager', 'mobManagerImpl');
    }

    /**
     * ClassLoader used by this implementation.
     *
     * @return ClassLoaderImpl
     */
    function getClassLoader() {
        return $this->classLoader;
    }

    /**
     * Enforces the manager to not be started.
     *
     * @throws MobException
     * @return void
     */
    private function requiresNotStarted() {
        $this->requiresNotStopped();
        if ($this->started) {
            throw new MobException("MobManager is not started yet.");
        }
    }

    /**
     * Enforces the manager to be started.
     *
     * @throws MobException
     * @return void
     */
    private function requiresStarted() {
        $this->requiresNotStopped();
        if (!$this->started) {
            throw new MobException("MobManager is started.");
        }
    }

    /**
     * Enforces the manager to be running.
     *
     * @throws MobException
     * @return void
     */
    private function requiresNotStopped() {
        if ($this->stopped) {
            throw new MobException("MobManaged stopped.");
        }
    }

    /**
     * Adds a directory for automatic discovery of mobs.
     *
     * @param string $dir
     * @return void
     */
    public function addDirectory($dir) {
        $this->requiresNotStarted();

        $classes = $this->classLoader->addPath($dir);

        // look for extension classes
        foreach ($classes as $class) {
            if ($class->isAbstract()) continue;
            if ($class->isInterface()) continue;

            if ($class->implementsInterface('MobManagerExtension')) {
                $this->registerExtension($class);
            } else {
                $classAnnotations = $this->getAnnotationParser()->getAnnotatedClass($class);
                if ($classAnnotations->hasAnnotation('name')) {
                    $this->registerMob($class);
                }
            }
        }
    }

    /**
     * Provides the implementation used for annotation parsing.
     *
     * @return AnnotationParser
     */
    public function getAnnotationParser() {
        return $this->annotationParser;
    }

    /**
     * Get an extension by its concrete name.
     *
     * @param string $name
     * @return MobManagerExtension
     * @throws MobException if extension does not exist
     */
    public function getExtension($name) {
        if (!isset($this->extensions[$name])) {
            throw new MobException("Extension $name does not exist.");
        }
        return $this->extensions[$name];
    }

    /**
     * A list of all registered extensions.
     *
     * @return MobManagerExtension[]
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * Retrieves a mob by its name. If there is no mob or more than one mob, this will throw an exception.
     *
     * @param string $name the mob's name
     * @return mixed the retrieved mob
     * @throws MobException if the mob is not found
     */
    public function getMob($name) {
        $this->requiresStarted();

        if (!isset($this->names[$name])) {
            throw new MobException("No managed object $name registered.");
        }

        $mobNames = $this->names[$name];
        if (count($mobNames) != 1) {
            throw new MobException("Managed object $name is not unique identifiable.");
        }

        $mobName = $mobNames[0];
        if (isset($this->mobs[$mobName])) {
            return $this->mobs[$mobName];
        } else {
            return $this->objects[$mobName];
        }
    }

    /**
     * Retrieves all mobs by their name.
     *
     * @param string $name the mob's name
     * @return array the retrieved mobs
     */
    public function getMobs($name) {
        $this->requiresStarted();

        if (!isset($this->names[$name])) {
            return array();
        }

        $mobNames = $this->names[$name];
        $mobs = array();
        foreach ($mobNames as $mobName) {
            if (isset($this->mobs[$mobName])) {
                $mobs[] = $this->mobs[$mobName];
            } else {
                $mobs[] = $this->objects[$mobName];
            }
        }

        return $mobs;
    }

    /**
     * Checks if a managed object exists in the manager.
     *
     * @abstract
     * @param string $name
     * @return bool
     */
    public function hasMob($name) {
        $this->requiresStarted();

        if (!isset($this->names[$name])) {
            return false;
        }

        $mobNames = $this->names[$name];
        if (count($mobNames) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the extension exists.
     *
     * @param string $name
     * @return boolean
     */
    public function hasExtension($name) {
        return isset($this->extensions[$name]);
    }

    /**
     * Registers an extension on the manager.
     *
     * @param ReflectionClass $class
     * @return void
     */
    public function registerExtension(ReflectionClass $class) {
        $this->requiresNotStarted();

        if ($this->hasExtension($class->getName())) {
            throw new MobException("Extension ".$class->getName()." already registered.");
        }

        if (!$class->implementsInterface('MobManagerExtension')) {
            throw new MobException($class->getName()." is not a MobManagerExtension.");
        }
        if ($class->isAbstract()) {
            throw new MobException("Extension is abstract.");
        }
        if ($class->isInterface()) {
            throw new MobException("Extension is an interface.");
        }

        $extension = $class->newInstance();
        $extension->setMobManager($this);
        $this->extensions[$class->getName()] = $extension;
    }

    /**
     * Registers an instance in the manager.
     *
     * @param ReflectionClass $class
     * @return void
     */
    public function registerMob(ReflectionClass $class) {
        $this->requiresNotStarted();

        if ($class->implementsInterface('MobManagerExtension')) {
            throw new MobException($class->getName()." is a MobManagerExtension.");
        }
        if ($class->isAbstract()) {
            throw new MobException($class->getName()." is abstract.");
        }
        if ($class->isInterface()) {
            throw new MobException($class->getName()." is an interface.");
        }

        $classAnnotations = $this->getAnnotationParser()->getAnnotatedClass($class);
        if (!$classAnnotations->hasAnnotation('name')) {
            throw new MobException($class->getName()." is not marked as a managed object.");
        }

        $name = $classAnnotations->getAnnotation('name');
        if (trim($name->getPayload()) != '') {
            $name = trim($name->getPayload());
        } else {
            $name = strtolower(substr($class->getName(), 0, 1)).substr($class->getName(), 1);
        }

        if (isset($this->objects[$name]) || isset($this->mobClasses[$name])) {
            throw new MobException("Managed object ".$class->getName()." already registered.");
        }

        $this->mobClasses[$name] = $class;

        $this->registerName($name, $name);
    }

    /**
     * Sets a name for a registered mob.
     *
     * @param string $name
     * @param string $mobName
     * @return void
     */
    public function registerName($name, $mobName) {
        $this->requiresNotStarted();

        if (!isset($this->mobClasses[$mobName]) && !isset($this->objects[$mobName])) {
            throw new MobException("Unknown managed object $mobName referenced.");
        }

        if (!isset($this->names[$name])) {
            $this->names[$name] = array();
        }

        if (!in_array($mobName, $this->names[$name])) {
            $this->names[$name][] = $mobName;
        }
    }

    /**
     * Inserts an instantiated object into the manager.
     *
     * @abstract
     * @param string $mobName
     * @param mixed $object
     * @return void
     */
    public function registerObject($mobName, $object) {
        $this->requiresNotStarted();

        if (isset($this->objects[$mobName]) || isset($this->mobClasses[$mobName])) {
            throw new MobException("$mobName name is already registered.");
        }

        $this->objects[$mobName] = $object;
    }

    /**
     * Starts the manager, extensions cannot get registered anymore at this point.
     *
     * @return void
     */
    public function start() {
        // load all proxies
        $proxies = array();
        foreach ($this->mobClasses as $name => $class) {
            $proxy = new MobProxyImpl($this, $name, $class);
            $proxies[] = $proxy;

            $instance = ObjectProxyGenerator::generateObject($class->getName(), $proxy);

            $this->mobs[$name] = $instance;
        }

        foreach ($proxies as $proxy) {
            $proxy->onLoad();
        }

        $this->started = true;

        if ($this->hasMob('eventManager')) {
            $eventManager = $this->getMob('eventManager');
            $eventManager->fire('manager.started');
        }
    }

    /**
     * Stops the manager.
     *
     * @return void
     */
    public function stop() {
        if ($this->hasMob('eventManager')) {
            $eventManager = $this->getMob('eventManager');
            $eventManager->fire('manager.stopping');
            $eventManager->fire('manager.stopped');
        }
    }

    /**
     * Provides singleton access to the MobManager; do not use!
     *
     * @static
     * @return MobManagerImpl
     */
    public static function getSingleton() {
        if (!self::$SINGLETON) {
            throw new MobException("No singleton registered yet.");
        }
        return self::$SINGLETON;
    }

    /**
     * Creates a new MobManager.
     *
     * @static
     * @return MobManager
     */
    public static function create() {
        return new MobManagerImpl();
    }

    /**
     * Creates a new manager and automatically starts it.
     *
     * @static
     * @param array|string $dirs
     * @return MobManager
     */
    public static function autostart($dirs) {
        $manager = self::create();

        if (is_array($dirs)) {
            foreach ($dirs as $dir) {
                $manager->addDirectory($dir);
            }
        } else {
            $manager->addDirectory($dirs);
        }

        $manager->start();
        return $manager;
    }

    function __toString() {
        return 'MobManagerImpl';
    }
}
