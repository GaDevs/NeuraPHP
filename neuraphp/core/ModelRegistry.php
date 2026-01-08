<?php
// /neuraphp/core/ModelRegistry.php
namespace NeuraPHP\Core;

class ModelRegistry
{
    protected array $models = [];

    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    public static function loadFromConfig(string $file): self
    {
        $models = file_exists($file) ? require $file : [];
        return new self($models);
    }

    public function get(string $provider, string $type): ?string
    {
        return $this->models[$provider][$type] ?? null;
    }

    public function all(): array
    {
        return $this->models;
    }
}
