<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\Cache;

class CacheTest extends TestCase
{
    public function testCacheStress()
    {
        $cache = new Cache(__DIR__ . '/../../neuraphp/cache');
        for ($i = 0; $i < 1000; $i++) {
            $cache->set('key' . $i, 'value' . $i, 60);
        }
        $this->assertEquals('value999', $cache->get('key999'));
    }
}
