<?php
/*
 * This file is part of the Stash package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stash\Driver;

use Stash\Exception\RuntimeException;
use Stash\Interfaces\DriverInterface;

/**
 * StashSqlite is a wrapper around the xcache php extension, which allows developers to store data in memory.
 *
 * @package Stash
 * @author  Robert Hafner <tedivm@tedivm.com>
 *
 * @codeCoverageIgnore Just until I figure out how to get phpunit working over http, or xcache over cli
 */
class Xcache implements DriverInterface
{
    /**
     * @var int
     */
    protected $ttl = 300;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var bool
     */
    protected $commandLineMode = false;

    /**
     * @param array $options
     *
     * @throws \Stash\Exception\RuntimeException
     */
    public function __construct(array $options = array())
    {
        if (!static::isAvailable()) {
            throw new RuntimeException('Extension is not installed.');
        }

        if (isset($options['ttl']) && is_numeric($options['ttl'])) {
            $this->ttl = (int) $options['ttl'];
        }

        $this->namespace = isset($options['namespace']) ? $options['namespace'] : md5(__FILE__);
        $this->commandLineMode = php_sapi_name() === 'cli';
    }

    /**
     * Empty destructor to maintain a standardized interface across all drivers.
     */
    public function __destruct()
    {
    }

    /**
     * Set the {@see $commandLineMode} property.
     *
     * @param boolean $commandLineMode
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function setCommandLineMode($commandLineMode)
    {
        $this->commandLineMode = $commandLineMode;
        return $this;
    }

    /**
     * This function should return the data array, exactly as it was received by the storeData function, or false if it
     * is not present. This array should have a value for "createdOn" and for "return", which should be the data the
     * main script is trying to store.
     *
     * @param  array $key
     * @return array
     */
    public function getData($key)
    {
        if ($this->isCommandLineMode()) {
            return false;
        }

        $keyString = $this->makeKey($key);
        if (!$keyString) {
            return false;
        }

        if (!xcache_isset($keyString)) {
            return false;
        }

        $data = xcache_get($keyString);

        return unserialize($data);
    }

    /**
     * This function takes an array as its first argument and the expiration time as the second. This array contains two
     * items, "createdOn" describing the first time the item was called and "return", which is the data that needs to be
     * stored. This function needs to store that data in such a way that it can be retrieved exactly as it was sent. The
     * expiration time needs to be stored with this data.
     *
     * @param array $key
     * @param array $data
     * @param int   $expiration
     *
     * @return bool
     */
    public function storeData($key, $data, $expiration)
    {
        if ($this->isCommandLineMode()) {
            return false;
        }

        $keyString = $this->makeKey($key);
        if (!$keyString) {
            return false;
        }

        $cacheTime = $this->getCacheTime($expiration);

        return xcache_set($keyString, serialize(array('data' => $data, 'expiration' => $expiration)), $cacheTime);
    }

    /**
     * This function should clear the cache tree using the key array provided. If called with no arguments the entire
     * cache needs to be cleared.
     *
     * @param  array $key
     * @return bool
     */
    public function clear($key = null)
    {
        if ($this->isCommandLineMode()) {
            return false;
        }

        if ($key === null) {
            $key = array();
        }
        $keyString = $this->makeKey($key);
        if (!$keyString) {
            return false;
        }

        return xcache_unset_by_prefix($keyString);
    }

    /**
     * This function is used to remove expired items from the cache.
     *
     * @return bool
     */
    public function purge()
    {
        return $this->clear();
    }

    /**
     * This function checks to see if it is possible to enable this driver.
     *
     * @return bool true
     */
    public static function isAvailable()
    {
        return extension_loaded('xcache');
    }

    /**
     * @param array $key
     *
     * @return string
     */
    protected function makeKey($key)
    {
        $keyString = md5(__FILE__) . '::'; // make it unique per install

        if (isset($this->namespace)) {
            $keyString .= $this->namespace . '::';
        }

        foreach ($key as $piece) {
            $keyString .= $piece . '::';
        }

        return $keyString;
    }

    /**
     * @param $expiration
     *
     * @return int
     */
    protected function getCacheTime($expiration)
    {
        $life = $expiration - time(true);

        return $this->ttl > $life ? $this->ttl : $life;
    }

    /**
     * @return bool
     */
    protected function isCommandLineMode()
    {
        return $this->commandLineMode;
    }
}
