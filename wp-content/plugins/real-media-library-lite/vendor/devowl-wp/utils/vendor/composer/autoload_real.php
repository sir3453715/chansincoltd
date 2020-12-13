<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit22c87c62ff5217c6dad81f833a994425 {
    private static $loader;

    public static function loadClassLoader($class) {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader() {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(
            ['ComposerAutoloaderInit22c87c62ff5217c6dad81f833a994425', 'loadClassLoader'],
            true,
            true
        );
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(['ComposerAutoloaderInit22c87c62ff5217c6dad81f833a994425', 'loadClassLoader']);

        $useStaticLoader =
            PHP_VERSION_ID >= 50600 &&
            !defined('HHVM_VERSION') &&
            (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require __DIR__ . '/autoload_static.php';

            call_user_func(
                \Composer\Autoload\ComposerStaticInit22c87c62ff5217c6dad81f833a994425::getInitializer($loader)
            );
        } else {
            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        return $loader;
    }
}