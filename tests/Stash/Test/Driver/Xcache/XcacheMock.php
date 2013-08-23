<?php
/**
 * Stash
 *
 * Copyright (c) 2012-2013, Trivago GmbH
 * All rights reserved.
 *
 * @since 23.08.13
 * @author Innovation Center Leipzig <team.leipzig@trivago.com>
 * @author Roman Lasinski <roman.lasinski@trivago.com>
 * @copyright 2012-2013 Trivago GmbH
 */
namespace Stash\Driver;

/**
 * Class Mock
 *
 * @package Stash\Test\Driver\Xcache
 * @author Innovation Center Leipzig <team.leipzig@trivago.com>
 * @author Roman Lasinski <roman.lasinski@trivago.com>
 * @copyright 2012-2013 Trivago GmbH
 */
class XcacheMock
{
    static protected $data = array();

    static public function unsetByPrefix($prefix)
    {
        foreach (self::$data as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                unset(self::$data[$key]);
            }
        }
        return true;
    }

    static public function set($key, $data, $lifeTime)
    {
        self::$data[$key] = $data;
        return true;
    }

    static public function get($key)
    {
        return self::$data[$key];
    }

    static public function has($key)
    {
        return isset(self::$data[$key]);
    }

    static public function clear()
    {
        self::$data = array();
        return true;
    }
}

// Override global xcache functions
function xcache_unset_by_prefix($prefix)
{
    return XcacheMock::unsetByPrefix($prefix);
}

function xcache_set($key, $data, $lifeTime)
{
    return XcacheMock::set($key, $data, $lifeTime);
}
function xcache_get($key)
{
    return XcacheMock::get($key);
}
function xcache_isset($key)
{
    return XcacheMock::has($key);
}
