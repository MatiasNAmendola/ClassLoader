<?php

class ClassLoader {
    private static $loader;
    private $path;
    
    private function __construct($path) {
        if(null === $path) {
            $path = __DIR__;
        }
        
        $this->addPath($path);
        spl_autoload_register(array($this, 'loadClass'));
    }
    
    public static function getLoader($path = null) {
        if(empty(static::$loader)) {
            static::$loader = new ClassLoader($path);
        }
        
        return static::$loader;
    }
    
    public function addPath($path) {
        if(is_array($path)) {
            foreach($path as $pathItem) {
                $this->addPath($pathItem);
            }
        } else if(is_string($path)) {
            $this->path[] = $path;
        } else {
            // Do nothing.
        }
    }
    
    public function loadClass($className) {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        
        if($classPath = $this->getClassPath($fileName)) {
            require $classPath;
        }
    }
    
    private function getClassPath($className) {
        foreach($this->path as $path) {
            $classPath = $path.DIRECTORY_SEPARATOR.$className;
            if(file_exists($classPath)) {
                return $classPath;
            }
        }
        
        return null;
    }
}

