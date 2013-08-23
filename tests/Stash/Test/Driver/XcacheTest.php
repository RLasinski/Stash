<?php
/*
 * This file is part of the Stash package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stash\Test\Driver;

include 'Xcache/XcacheMock.php';

class XcacheTest extends AbstractDriverTest
{
    protected $driverClass = 'Stash\Driver\Xcache';

    public function testConstructor()
    {
        $driverType = $this->driverClass;
        $options = $this->getOptions();
        $options['namespace'] = 'namespace_test';
        $options['ttl'] = 15;
        $driver = new $driverType($options);

        $this->assertAttributeEquals('namespace_test', 'namespace', $driver, 'Xcache is setting supplied namespace.');
        $this->assertAttributeEquals(15, 'ttl', $driver, 'Xcache is setting supplied ttl.');

        return parent::testConstructor();
    }
}
