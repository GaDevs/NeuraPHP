<?php
/**
 * NeuraPHP AI Engine™
 * Rate Limiter
 *
 * @package NeuraPHP\Core
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Core;

/**
 * Class RateLimit
 * Simple file-based rate limiter
 */
class RateLimit
{
    /**
     * @var string Rate limit directory
     */
    protected string $rateDir;

    /**
     * RateLimit constructor
     * @param string|null $rateDir
     */
    public function __construct(?string $rateDir = null)
    {
        $this->rateDir = $rateDir ?? __DIR__ . '/../../rate';
        if (!is_dir($this->rateDir)) {
            mkdir($this->rateDir, 0775, true);
        }
    }

    /**
     * Check and increment rate limit for a key
     * @param string $key
     * @param int $limit
     * @param int $windowSeconds
     * @return bool True if allowed, false if rate limited
     */
    public function check(string $key, int $limit = 60, int $windowSeconds = 60): bool
    {
        $file = $this->rateDir . '/' . md5($key) . '.rate';
        $now = time();
        $data = [ 'start' => $now, 'count' => 0 ];
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            if ($now - $data['start'] > $windowSeconds) {
                $data = [ 'start' => $now, 'count' => 0 ];
            }
        }
        $data['count']++;
        file_put_contents($file, serialize($data));
        return $data['count'] <= $limit;
    }
}
