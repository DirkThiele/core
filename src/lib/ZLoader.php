<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

include 'lib/i18n/ZGettextFunctions.php';
include 'lib/Zikula/KernelClassLoader.php';
include 'lib/debug.php';

define('ZLOADER_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

// setup vendors in include path
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);

include 'Smarty/Smarty.class.php';

/**
 * ZLoader.
 */
class ZLoader
{
    /**
     * Map.
     *
     * @var array
     */
    private static $map;

    /**
     * Autoloaders.
     *
     * @var Zikula_KernelClassLoader
     */
    private static $autoloaders;

    /**
     * Base setup.
     *
     * @return void
     */
    public static function register()
    {
        self::$map = self::map();
        spl_autoload_register(array('ZLoader', 'autoload'));
        self::$autoloaders = new Zikula_KernelClassLoader();
        self::$autoloaders->spl_autoload_register();

        // Setup EventManager and ServiceManager
        $em = EventUtil::getManager(ServiceUtil::getManager());

        self::addAutoloader('Doctrine', ZLOADER_PATH . '/vendor/Doctrine');
        self::addAutoloader('Categories', 'system/Categories/lib');
        //self::addAutoloader('Zend', ZLOADER_PATH . '/vendor');
        include ZLOADER_PATH. 'legacy/Loader.php';

        // load eventhandlers from config/EventHandlers directory if any.
        EventUtil::attachCustomHandlers('config/EventHandlers');

        // setup core events.
        EventUtil::attach('core.init', array('SystemListenersUtil', 'sessionLogging'));
        EventUtil::attach('core.init', array('SystemListenersUtil', 'systemPlugins'));
        EventUtil::attach('core.postinit', array('SystemListenersUtil', 'systemHooks'));
    }

    /**
     * Add new autoloader to the stack.
     *
     * @param string $namespace Namespace.
     * @param string $path      Path.
     * @param string $separator Separator, _ or \\.
     *
     * @return void
     */
    public static function addAutoloader($namespace, $path = '', $separator = '_')
    {
        if (self::$autoloaders->hasAutoloader($namespace)) {
            return;
        }

        self::$autoloaders->register($namespace, $path, $separator);
    }

    /**
     * Simple PEAR autoloader and handling for non-PEAR classes.
     *
     * @param string $class Class name.
     *
     * @return boolean
     */
    public static function autoload($class)
    {
        // load from maps
        $map = self::$map;
        if (isset($map[$class])) {
            $path = ZLOADER_PATH . "$map[$class]/$class.php";
            if (file_exists($path)) {
                return include $path;
            }
        }

        // Classloader for SystemPlugin
        if (strpos($class, 'SystemPlugin') === 0) {
            $array = explode('_', $class);
            $pluginName = $array[1];
            $base = realpath(ZLOADER_PATH . '/../plugins');
            $name = substr($class, strlen("SystemPlugin_${$pluginName}"), strlen($class));
            $path = str_replace('_', '/', "$base/$pluginName/lib/$name.php");
            if (file_exists($path)) {
                return include $path;
            }
        }

        // Classloader for ModulePlugin
        if (strpos($class, 'ModulePlugin') === 0) {
            $array = explode('_', $class);
            $moduleName = $array[1];
            $pluginName = $array[2];
            $modinfo = ModUtil::getInfoFromName($moduleName);
            $base = ($modinfo['type'] == ModUtil::TYPE_MODULE) ? 'modules' : 'system';
            $name = substr($class, strlen("ModulePlugin_${moduleName}_${pluginName}") + 1, strlen($class));
            $path = str_replace('_', '/', "$base/$moduleName/plugins/$pluginName/lib/$pluginName/$name.php");
            if (file_exists($path)) {
                return include $path;
            }
        }

        // generic PEAR style namespace to path, i.e Foo_Bar -> Foo/Bar.php
        if (strpos($class, '_')) {
            $array = explode('_', $class);
            $prefix = (isset($map[$array[0]]) ? $map[$array[0]] . '/' : '');
            $path = ZLOADER_PATH . $prefix . str_replace('_', '/', $class) . '.php';
            if (file_exists($path)) {
                return include $path;
            }
        }

        $file = "lib/$class.php";
        if (file_exists($file)) {
            return include $file;
        }
    }

    /**
     * Provides map for simple autoloader.
     *
     * @return array Class locations.
     */
    public static function map()
    {
        return array(
            'ZLanguage' => 'i18n',
            'ZI18n' => 'i18n',
            'ZL10n' => 'i18n',
            'ZLocale' => 'i18n',
            'ZGettext' => 'i18n',
            'ZMO' => 'i18n',
            'ZLanguageBrowser' => 'i18n',
            'DBObject' => 'dbobject',
            'DBObjectArray' => 'dbobject',
            'DBUtil' => 'util',
            'BlockUtil' => 'util',
            'DBConnectionStack' => 'util',
            'AjaxUtil' => 'util',
            'CacheUtil' => 'util',
            'CategoryRegistryUtil' => 'util',
            'CategoryUtil' => 'util',
            'CookieUtil' => 'util',
            'DataUtil' => 'util',
            'DateUtil' => 'util',
            'DoctrineUtil' => 'util',
            'EventUtil' => 'util',
            'FileUtil' => 'util',
            'FilterUtil' => 'util',
            'FormUtil' => 'util',
            'HtmlUtil' => 'util',
            'LogUtil' => 'util',
            'ModUtil' => 'util',
            'ObjectUtil' => 'util',
            'PluginUtil' => 'util',
            'PageUtil' => 'util',
            'RandomUtil' => 'util',
            'SecurityUtil' => 'util',
            'ServiceUtil' => 'util',
            'SessionUtil' => 'util',
            'StringUtil' => 'util',
            'ThemeUtil' => 'util',
            'UserUtil' => 'util',
            'ValidationUtil' => 'util',
            'SystemListenersUtil' => 'util',
            'Loader' => 'legacy',
            );
    }
}
