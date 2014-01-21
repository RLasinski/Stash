<?php
/*
 * This file is part of the Stash package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stash\Interfaces;

use Psr\Log\LoggerAwareInterface;

/**
 * @package Stash
 * @author  Robert Hafner <tedivm@tedivm.com>
 */
interface PoolInterface extends LoggerAwareInterface
{
    /**
     * Takes the same arguments as the Stash->setupKey() function and returns with a new Stash object. If a driver
     * has been set for this class then it is used, otherwise the Stash object will be set to use script memory only.
     *
     * @example  $cache = $pool->getItem('permissions', 'user', '4', '2');
     *
     * @internal param array|string $key , $key, $key...
     * @return \Stash\Interfaces\ItemInterface
     */
    public function getItem();

    /**
     * Returns a group of cache objects as an \Iterator
     *
     * Bulk lookups can often by streamlined by backend cache systems. The
     * returned iterator will contain a Stash\Item for each key passed.
     *
     * @param  array     $keys
     * @return \Iterator
     */
    public function getItemIterator($keys);

    /**
     * Empties the entire cache pool of all items.
     *
     * @return bool success
     */
    public function flush();

    /**
     * The Purge function allows drivers to perform basic maintenance tasks,
     * such as removing stale or expired items from storage. Not all drivers
     * need this, as many interact with systems that handle that automatically.
     *
     * It's important that this function is not called from inside a normal
     * request, as the maintenance tasks this allows can occasionally take some
     * time.
     *
     * @return bool success
     */
    public function purge();

    /**
     * Sets a driver for each Stash object created by this class. This allows
     * the drivers to be created just once and reused, making it much easier to incorporate caching into any code.
     *
     * @param DriverInterface $driver
     */
    public function setDriver(DriverInterface $driver);

    /**
     * @return DriverInterface
     */
    public function getDriver();
}
