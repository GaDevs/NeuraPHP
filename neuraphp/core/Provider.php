<?php
namespace NeuraPHP\Core;

/**
 * Exception thrown when a provider is not registered.
 */
class ProviderNotRegisteredException extends \Exception {}
/**
 * NeuraPHP AI Engine™
 * Provider Manager
 *
 * @package NeuraPHP\Core
 * @author  NeuraPHP Team
 * @license MIT
 */

use Exception;

/**
 * Class Provider
 * Manages AI providers for the SDK
 */
class Provider
{
    /**
     * @var array Registered providers
     */
    protected array $providers = [];

    /**
     * Register a new provider
     * @param string $name
     * @param object $provider
     * @return void
     */
    public function register(string $name, object $provider): void
    {
        $this->providers[$name] = $provider;
    }

    /**
     * Get a registered provider
     * @param string $name
     * @return object
     * @throws Exception
     */
    public function get(string $name): object
    {
        if (!isset($this->providers[$name])) {
            throw new ProviderNotRegisteredException("Provider '$name' not registered.");
        }
        return $this->providers[$name];
    }

    /**
     * List all registered providers
     * @return array
     */
    public function all(): array
    {
        return $this->providers;
    }
}
