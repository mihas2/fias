<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit53868f1e73be77a2acc806c5b2185592
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Fias\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Fias\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit53868f1e73be77a2acc806c5b2185592::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit53868f1e73be77a2acc806c5b2185592::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}