<?php

namespace ssa;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\MemcacheCache;

/**
 * Description of Configuration
 *
 * @author thomas
 */
class Configuration {
    
    /**
     * debug or not debug
     * 
     * Warning if debug is false, many change in your code must not be detected.
     * Put debug = false when you are in prod environement
     * 
     * default true
     * @var boolean
     */
    private $debug;
    
    /**
     * the cache mode 
     * file
     * apc warning you must have apc installed else error occurs
     * memcache
     * no
     * 
     * @var string 
     */
    private $cacheMode;
    
    /**
     * use if cache mode is file
     * this is the cache directory 
     * @var string  
     */
    private $cacheDirectory;
    
    /**
     *
     * @var string
     */
    private  $memcacheHost;
    
    /**
     *
     * @var int
     */
    private  $memcachePort;
    
    /**
     *
     * @var CacheProvider
     */
    private $cacheProvider;
    
    /**
     *
     * @var Configuration
     */
    private static $instance;
    

    
    /**
     * get the configuration manager
     * 
     * @return Configuration
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Configuration();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->setDebug(true);
        $this->setCacheMode('none');
    }
    
    /**
     * configure de framework
     * 
     * @param array $array array with debug, cacheMode, cacheDirectory 
     */
    public function configure(array $array) {
        if (isset($array['debug'])) {
            $this->setDebug($array['debug']);
        }
        if (isset($array['cacheMode'])) {
            $this->setCacheMode($array['cacheMode']);
        }
        if (isset($array['cacheDirectory'])) {
            $this->setCacheDirectory($array['cacheDirectory']);
        }
        if (isset($array['memcacheHost'])) {
            $this->setMemcacheHost($array['memcacheHost']);
        }
        if (isset($array['memcachePort'])) {
            $this->setMemcachePort($array['memcachePort']);
        }
    }
    
    public function getCacheProvider() {
        if ($this->cacheProvider == null) {
            $cacheMode = $this->getCacheMode();
            if ($cacheMode == 'file') {
                $this->cacheProvider = new PhpFileCache($this->getCacheDirectory());
            } else if ($cacheMode == 'apc') {
                $this->cacheProvider = new ApcCache();
            } else if ($cacheMode == 'memcache') {
                $memcache = new Memcache();
                $memcache->connect($this->getMemcacheHost(), $this->getMemcachePort());
                $cacheDriver = new \Doctrine\Common\Cache\MemcacheCache();
                $cacheDriver->setMemcache($memcache);
                $this->cacheProvider = $cacheDriver;
            } else {
                $this->cacheProvider = null;
            }
        }
        return $this->cacheProvider;
    }
    /**
     * 
     * @param string $cacheDirectory
     */
    public function setCacheDirectory($cacheDirectory) {
        $this->cacheDirectory = $cacheDirectory;
    }
    
    /**
     * 
     * @param string $cacheMode
     */
    public function setCacheMode($cacheMode) {
        $this->cacheMode = $cacheMode;
    }
    
    /**
     * 
     * @param boolean $debug
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }
    /**
     * 
     * @return boolean
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * 
     * @return string
     */
    public function getCacheMode() {
        return $this->cacheMode;
    }

    /**
     * 
     * @return string
     */
    public function getCacheDirectory() {
        return $this->cacheDirectory;
    }

    public static function getMemcacheHost() {
        return $this->memcacheHost;
    }

    public static function getMemcachePort() {
        return $this->memcachePort;
    }

    public static function setMemcacheHost($memcacheHost) {
        $this->memcacheHost = $memcacheHost;
    }

    public static function setMemcachePort($memcachePort) {
        $this->memcachePort = $memcachePort;
    }


    
}
