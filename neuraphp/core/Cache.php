<?php
/**
 * NeuraPHP AI Engine™
 * Response Cache
 *
 * @package NeuraPHP\Core
 * @author  NeuraPHP Team
 * @license MIT
 */

namespace NeuraPHP\Core;

/**
 * Class Cache
 * Simple file-based response cache
 */
class Cache
{
    /**
     * @var string Cache directory
     */
    protected string $cacheDir;

    /**
     * Cache constructor
     * @param string|null $cacheDir
     */
    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? __DIR__ . '/../../cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }
    }

    /**
     * Generate cache key
     * @param string $key
     * @return string
     */
    protected function key(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    /**
     * Set cache value
     * @param string $key
     * @param mixed $value
     * @param int $ttl Seconds to live
     * @return void
     */
    public function set(string $key, $value, int $ttl = 300): void
    {
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];
        file_put_contents($this->key($key), serialize($data));
    }

    /**
     * Get cache value
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $file = $this->key($key);
        if (!file_exists($file)) {
            return null;
        }
        $data = unserialize(file_get_contents($file));
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        return $data['value'];
    }

    /**
     * Delete cache value
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $file = $this->key($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
