<?php
use PHPUnit\Framework\TestCase;
use NeuraPHP\Core\ProviderFactory;

class ProviderFactoryTest extends TestCase
{
    public function testDynamicProviderSwitching()
    {
        $providersFile = __DIR__ . '/../../neuraphp/config/providers.php';
        $modelsFile = __DIR__ . '/../../neuraphp/config/models.php';
        $factory = ProviderFactory::fromConfig($providersFile, $modelsFile);
        $openai = $factory->create('openai', 'test-key');
        $gemini = $factory->create('gemini', 'test-key');
        $claude = $factory->create('claude', 'test-key');
        $this->assertInstanceOf('NeuraPHP\\Providers\\OpenAI', $openai);
        $this->assertInstanceOf('NeuraPHP\\Providers\\Gemini', $gemini);
        $this->assertInstanceOf('NeuraPHP\\Providers\\Claude', $claude);
    }
}
