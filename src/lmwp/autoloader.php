<?php

namespace lmwp;


class Autoloader
{
    /** @var array key: namespace prefix, value: array of base directories */
    private $prefixMaps = array();

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function loadClass($class)
    {
        $prefix = $class;

        while (false !== ($pos = strrpos($prefix, '\\'))) {

            $prefix = substr($class, 0, $pos + 1);

            $relativeClass = substr($class, $pos + 1);

            $mappedFile = $this->loadMappedFile($prefix, $relativeClass);
            if ($mappedFile) {
                return $mappedFile;
            }

            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    public function loadMappedFile($prefix, $relativeClass)
    {
        if (isset($this->prefixMaps[$prefix]) === false) {
            return false;
        }

        foreach ($this->prefixMaps[$prefix] as $baseDir) {
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    private function requireFile($file)
    {
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;

            return true;
        }

        return false;
    }

    public function addNamespace($prefix, $baseDir, $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';

        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';

        if (isset($this->prefixMaps[$prefix]) === false) {
            $this->prefixMaps[$prefix] = array();
        }

        if($prepend) {
            array_unshift($this->prefixMaps[$prefix], $baseDir);
        } else {
            array_push($this->prefixMaps[$prefix], $baseDir);
        }
    }
}