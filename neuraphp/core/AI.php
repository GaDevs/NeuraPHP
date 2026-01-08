<?php
namespace NeuraPHP\Core;

/**
 * Exception thrown when a core component is not found.
 */
class ComponentNotFoundException extends \Exception {}
/**
 * NeuraPHP AI Engine™
 * Universal AI Infrastructure SDK for PHP Applications
 *
 * @package NeuraPHP\Core
 * @author  NeuraPHP Team
 * @license MIT
 */

use Exception;

/**
 * Class AI
 * SDK Loader and entry point for NeuraPHP AI Engine™
 */
use NeuraPHP\Core\ProviderFactory;
use NeuraPHP\Core\ModelRegistry;
use NeuraPHP\Core\ProviderInterface;

class AI
{
    /**
     * @var array Configuration array
     */
    protected array $config = [];

    /**
     * @var AI|null Singleton instance
     */
    protected static ?AI $instance = null;

    /**
     * @var ProviderFactory
     */
    protected ProviderFactory $providerFactory;

    /**
     * @var ProviderInterface|null
     */
    protected ?ProviderInterface $provider = null;

    /**
     * AI constructor (protected for singleton)
     * @param array $config
     */
    protected function __construct(array $config = [])
    {
        $this->config = $config;
        $providersFile = $config['providers_file'] ?? __DIR__ . '/../config/providers.php';
        $modelsFile = $config['models_file'] ?? __DIR__ . '/../config/models.php';
        $this->providerFactory = ProviderFactory::fromConfig($providersFile, $modelsFile);
    }

    /**
     * Get singleton instance
     * @param array $config
     * @return AI
     */
    public static function getInstance(array $config = []): AI
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Get configuration value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set configuration value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Set the provider instance dynamically
     * @param string $providerKey
     * @param string $apiKey
     * @return void
     */
    public function setProvider(string $providerKey, string $apiKey): void
    {
        $this->provider = $this->providerFactory->create($providerKey, $apiKey);
    }

    /**
     * Get the current provider instance
     * @return ProviderInterface|null
     */
    public function getProvider(): ?ProviderInterface
    {
        return $this->provider;
    }

    /**
     * Load a core component (legacy)
     * @param string $component
     * @return object
     * @throws Exception
     */
    public function load(string $component): object
    {
        $class = __NAMESPACE__ . '\\' . $component;
        if (!class_exists($class)) {
            throw new ComponentNotFoundException("Component '$component' not found in core.");
        }
        return new $class();
    }
}
