<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\RateLimit;

class RateLimitBypassTest extends TestCase
{
    public function testRateLimitEnforcement()
    {
        $rate = new RateLimit(__DIR__ . '/../../neuraphp/rate');
        $ip = '127.0.0.1';
        $allowed = $rate->check('test_' . $ip, 1, 1);
        $this->assertTrue($allowed);
        $allowed2 = $rate->check('test_' . $ip, 1, 1);
        $this->assertFalse($allowed2);
    }
}
