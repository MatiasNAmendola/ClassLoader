<?php
/*
 * (c) 2012 Manuel Jesús Carrascosa de la Blanca
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 
 * @author Manuel Jesús Carrascosa de la Blanca <mjcarrascosa@gmail.com>
 */
class ClassLoader {
    /**
     *
     * @var ClassLoader 
     */
    private static $loader;
    /**
     *
     * @var array 
     */
    private $path;
    
    /**
     *
     * @var array 
     */
    private $classes;
    
    /**
     *
     * @param system $path 
     */
    private function __construct($path = null, $classes = null) {
        if(null === $path) {
            $path = __DIR__;
        }
        
        $this->addPath($path);
        spl_autoload_register(array($this, 'loadClass'));
    }
    
    /**
     *
     * @param string|array $path
     * @return ClassLoader 
     */
    public static function getLoader($path = null) {
        if(empty(static::$loader)) {
            static::$loader = new ClassLoader($path);
        }
        
        return static::$loader;
    }
    
    /**
     *
     * @param string|array $path 
     */
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
    
    /**
     *
     * @param string $className 
     */
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
    
    /**
     *
     * @param string $className
     * @return string|null 
     */
    private function getClassPath($className) {
        foreach($this->path as $path) {
            $classPath = $path.DIRECTORY_SEPARATOR.$className;
            if(file_exists($classPath)) {
                return $classPath;
            }
        }
        
        return null;
    }
    
    /**
     *
     * @param type $className
     * @param type $classPath
     * @return boolean 
     */
    public function setClassPath($className, $classPath) {
        if(file_exists($classPath)) {
            $this->classes[$className] = $classPath;
            return true;
        }
        
        return false;
    }
}

