<?php
/**
 * Created by PhpStorm.
 * @description: 获取class类工具类
 * @author: VAIO
 * @date: 2018-04-17 21:15
 */
namespace App\Http\Common;

class ClassUtils
{
    public const CLASS_EXT = '.php';
    public const APP_NAMESPACE = 'App';
    public const MODEL_RELATIVE_PATH = 'Http' . DIRECTORY_SEPARATOR .'Models';
    public const SERVICE_RELATIVE_PATH = 'Http' . DIRECTORY_SEPARATOR .'Services';
    public const CONTROLLER_RELATIVE_PATH = 'Http' . DIRECTORY_SEPARATOR . 'Controllers';

    private static $classes;
    private static $appPath;


    /**
     * 判断类存不存在
     *
     * @param $class
     * @param string $namespace
     * @return bool
     */
    public static function classExists($class, $namespace = '')
    {
        if ($namespace == '' || strpos($class, '\\') !== false) {
            return class_exists($class);
        }

        return class_exists($namespace . '\\' . $class);
    }

    /**
     * 根据model类名获取model
     *
     * @param $baseClassName
     * @return mixed
     */
    public static function getModel($baseClassName)
    {
        return static::findClass(static::getClasses(static::MODEL_RELATIVE_PATH), $baseClassName);
    }

    /**
     * 获取models
     *
     * @return mixed
     */
    public static function getModels()
    {
        return static::getClasses(static::MODEL_RELATIVE_PATH);
    }

    /**
     * 根据controller类名获取controllers
     *
     * @param $baseClassName
     * @return mixed
     */
    public static function getController($baseClassName)
    {
        return static::findClass(static::getClasses(static::CONTROLLER_RELATIVE_PATH), $baseClassName);
    }

    /**
     * 获取controllers
     *
     * @return mixed
     */
    public static function getControllers()
    {
        return static::getClasses(static::CONTROLLER_RELATIVE_PATH);
    }


    /**
     * 获取service
     *
     * @param $baseClassName
     * @return mixed
     */
    public static function getService($baseClassName)
    {
        return static::findClass(static::getClasses(static::SERVICE_RELATIVE_PATH), $baseClassName);
    }

    /**
     * 获取services
     *
     * @return mixed
     */
    public static function getServices()
    {
        return static::getClasses(static::SERVICE_RELATIVE_PATH);
    }

    /**
     * 根据APP的相对路径和class名称获取class
     *
     * @param $baseClassName
     * @param $relativePath
     * @return mixed
     */
    public static function getClass($baseClassName, $relativePath)
    {
        return static::findClass(static::getClasses($relativePath), $baseClassName);
    }

    /**
     * 根据APP的相对路径获取所有的class
     *
     * @param $relativePath
     * @return mixed
     */
    public static function getClasses($relativePath)
    {
        static::getAppPath();

        static::readDir(static::$appPath . DIRECTORY_SEPARATOR . $relativePath);

        return static::$classes;
    }

    /**
     * 寻找class
     *
     * @param array $classes
     * @param $needle
     * @return mixed
     */
    public static function findClass(array $classes, $needle) {
        if (!empty($classes)) {
            return array_map(function($class) use ($needle) {
                return $class['name'] === $needle && class_exists($class['class']);
            }, $classes);
        }

        throw new \RuntimeException('Class ' . $needle . ' not found');
    }

    /**
     * 读取路径下的所有文件
     *
     * @param $path
     */
    private static function readDir($path)
    {
        $dir = dir($path);

        while (false !== ($entry = $dir->read())) {
            if ($entry != '.' && $entry != '..') {
                $fileName = $path . '/' . $entry;

                if (is_dir($fileName)) {
                    static::readDir($fileName);
                } else {
                    $baseClassName = rtrim($entry, static::CLASS_EXT);
                    static::$classes[] = [
                        'name' => $baseClassName, 'class' => static::transformPathToNamespace($path) . '\\' . $baseClassName
                    ];
                }

            }
        }

        $dir->close();
    }

    /**
     * 通过路径转化成命名空间
     *
     * @param $path
     * @return string
     */
    private static function transformPathToNamespace($path)
    {
        $relativePath = str_replace(strtolower(static::$appPath), '', strtolower($path));
        $subNamespace = trim(str_replace('/', '\\', $relativePath), '\\');

        $tempArray = explode('\\', $subNamespace);
        array_walk($tempArray, function(&$str){
            ucfirst($str);
        });
        $standardSubNamespace = (empty($tempArray) ? '' : '\\' . implode('\\', $tempArray));

        return static::APP_NAMESPACE . $standardSubNamespace;
    }


    /**
     * 获取app路径
     */
    private static function getAppPath()
    {
        $dirName = dirname(__FILE__);
        $appIndex = strripos($dirName, static::APP_NAMESPACE);

        if ($appIndex !== false) {
            static::$appPath = substr($dirName, 0, $appIndex + strlen(static::APP_NAMESPACE));
            return;
        }

        throw new \RuntimeException("Wrong namespace :" . static::APP_NAMESPACE);
    }

}




