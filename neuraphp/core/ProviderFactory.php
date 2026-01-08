<?php
// /neuraphp/core/ProviderFactory.php
namespace NeuraPHP\Core;

use NeuraPHP\Providers\OpenAI;
use NeuraPHP\Providers\Gemini;
use NeuraPHP\Providers\Claude;

class ProviderFactory
{
    protected array $providers;
    protected ModelRegistry $modelRegistry;

    public function __construct(array $providers, ModelRegistry $modelRegistry)
    {
        $this->providers = $providers;
        $this->modelRegistry = $modelRegistry;
    }

    public static function fromConfig(string $providersFile, string $modelsFile): self
    {
        $providers = file_exists($providersFile) ? require $providersFile : [];
        $modelRegistry = ModelRegistry::loadFromConfig($modelsFile);
        return new self($providers, $modelRegistry);
    }

    public function create(string $providerKey, string $apiKey, ?string $model = null): ProviderInterface
    {
        $providerKey = strtolower($providerKey);
        $models = $this->modelRegistry->all();
        if ($model && isset($models[$providerKey])) {
            $models[$providerKey]['chat'] = $model;
        }
        switch ($providerKey) {
            case 'openai':
                return new OpenAI($apiKey, $models['openai'] ?? []);
            case 'gemini':
                return new Gemini($apiKey, $models['gemini'] ?? []);
            case 'claude':
                return new Claude($apiKey, $models['claude'] ?? []);
            default:
                throw new \Exception("Unknown provider: $providerKey");
        }
    }
}
