<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\AI;
use NeuraPHP\Core\Provider;
use NeuraPHP\Core\Cache;
use NeuraPHP\Core\RateLimit;
use NeuraPHP\Core\Memory;

class CoreTest extends TestCase
{
    public function testAIInstanceSingleton()
    {
        $ai1 = AI::getInstance(['foo' => 'bar']);
        $ai2 = AI::getInstance();
        $this->assertSame($ai1, $ai2);
        $this->assertEquals('bar', $ai1->getConfig('foo'));
    }

    public function testProviderRegisterAndGet()
    {
        $provider = new Provider();
        $mock = new \stdClass();
        $provider->register('mock', $mock);
        $this->assertSame($mock, $provider->get('mock'));
    }

    public function testCacheSetGetDelete()
    {
        $cache = new Cache(__DIR__ . '/tmp_cache');
        $cache->set('key', 'value', 2);
        $this->assertEquals('value', $cache->get('key'));
        $cache->delete('key');
        $this->assertNull($cache->get('key'));
    }

    public function testRateLimitCheck()
    {
        $rate = new RateLimit(__DIR__ . '/tmp_rate');
        $key = 'test';
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($rate->check($key, 3, 2));
        }
        $this->assertFalse($rate->check($key, 3, 2));
        sleep(3);
        $this->assertTrue($rate->check($key, 3, 2));
    }

    public function testMemoryPersistence()
    {
        $memory = new Memory(__DIR__ . '/tmp_memory');
        $cid = 'conv1';
        $history = [['role' => 'user', 'message' => 'hi', 'timestamp' => time()]];
        $memory->saveHistory($cid, $history);
        $this->assertEquals($history, $memory->getHistory($cid));
    }
}
