<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbd9e67872f8a001e8cf2426b7bc6abee
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'G' => 
        array (
            'Godruoyi\\Snowflake\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Godruoyi\\Snowflake\\' => 
        array (
            0 => __DIR__ . '/..' . '/godruoyi/php-snowflake/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slimdown' => 
            array (
                0 => __DIR__ . '/..' . '/jbroadway/slimdown',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbd9e67872f8a001e8cf2426b7bc6abee::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbd9e67872f8a001e8cf2426b7bc6abee::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitbd9e67872f8a001e8cf2426b7bc6abee::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitbd9e67872f8a001e8cf2426b7bc6abee::$classMap;

        }, null, ClassLoader::class);
    }
}
