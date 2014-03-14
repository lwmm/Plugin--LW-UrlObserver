<?php

namespace LwUrlObserver\Services;

class Autoloader
{

    public function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className) 
    {
        if (strstr($className, 'LwUrlObserver')) {
            $path = dirname(__FILE__).'/..';
            $filename = str_replace('LwUrlObserver', $path, $className);
        }
        
        $filename = str_replace('\\', '/', $filename).'.php';
        if (is_file($filename)) {
            include_once($filename);
        }
    }
}