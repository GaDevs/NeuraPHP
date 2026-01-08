<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\ModelRegistry;

class ModelRegistryTest extends TestCase
{
    public function testModelResolution()
    {
        $modelsFile = __DIR__ . '/../../neuraphp/config/models.php';
        $registry = ModelRegistry::loadFromConfig($modelsFile);
        $openaiChat = $registry->get('openai', 'chat');
        $this->assertNotEmpty($openaiChat);
    }
}
