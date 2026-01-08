<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\RateLimit;

class RateLimitTest extends TestCase
{
    public function testRateLimitStress()
    {
        $rate = new RateLimit(__DIR__ . '/../../neuraphp/rate');
        $ip = '127.0.0.1';
        $success = 0;
        for ($i = 0; $i < 20; $i++) {
            if ($rate->check('perf_' . $ip, 10, 1)) {
                $success++;
            }
        }
        $this->assertLessThanOrEqual(10, $success);
    }
}
