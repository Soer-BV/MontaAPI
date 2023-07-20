<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInite30c6f4c1c2bb83fcb2fdf1eabc5d06f
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInite30c6f4c1c2bb83fcb2fdf1eabc5d06f', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInite30c6f4c1c2bb83fcb2fdf1eabc5d06f', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInite30c6f4c1c2bb83fcb2fdf1eabc5d06f::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
