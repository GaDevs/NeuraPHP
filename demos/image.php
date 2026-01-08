<?php
// NeuraPHP AI Engine™ Image Generation Demo (Provider-aware)
require_once __DIR__ . '/_provider.php';
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../neuraphp/modules/Image.php';
require_once __DIR__ . '/../neuraphp/core/RateLimit.php';
require_once __DIR__ . '/../neuraphp/core/ProviderFactory.php';
require_once __DIR__ . '/../neuraphp/core/ModelRegistry.php';
require_once __DIR__ . '/../neuraphp/core/ProviderInterface.php';
use NeuraPHP\Modules\Image;
use NeuraPHP\Core\RateLimit;
use NeuraPHP\Core\ProviderFactory;

$rate = new RateLimit(__DIR__ . '/../neuraphp/rate');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
$allowed = $rate->check('image_demo_' . $ip, 5, 60);

$msg = '';
$file = '';
$debug = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['provider_select']) && $allowed && demo_provider_ready()) {
  $prompt = trim(filter_input(INPUT_POST, 'prompt', FILTER_DEFAULT));
  if ($prompt) {
    $providerKey = $_SESSION['provider'];
    $apiKey = $_SESSION['api_key'];
    $providersFile = __DIR__ . '/../neuraphp/config/providers.php';
    $modelsFile = __DIR__ . '/../neuraphp/config/models.php';
    $factory = ProviderFactory::fromConfig($providersFile, $modelsFile);
    $provider = $factory->create($providerKey, $apiKey);
    $img = new Image($provider);
    $file = $img->generate(htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8'));
    $msg = 'Image generated!';
    if (isset($provider) && method_exists($provider, 'getLastRawResponse')) {
      $debug['provider_raw'] = $provider->getLastRawResponse();
    }
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Image Generation Demo | NeuraPHP</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-lg mx-auto py-10">
    <h1 class="text-2xl font-bold mb-2">🖼️ Image Generation Demo</h1>
    <p class="mb-4 text-gray-600">Generate images from text prompts. Rate limited for safety.</p>
    <?php render_provider_form(); ?>
    <?php if (!demo_provider_ready()): ?>
      <div class="bg-yellow-100 text-yellow-700 p-3 rounded mb-4">Please select a provider and enter your API key to use the demo.</div>
    <?php endif; ?>
    <?php if (!$allowed): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">Rate limit exceeded. Please wait.</div>
    <?php endif; ?>
    <?php if ($msg): ?>
      <div class="bg-green-100 text-green-700 p-2 rounded mb-2"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <form method="post" class="flex gap-2 mb-4">
      <input name="prompt" required maxlength="100" class="flex-1 p-2 border rounded" placeholder="Describe your image..." <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>
      <button class="bg-blue-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>Generate</button>
    </form>
    <?php if ($file): ?>
      <div class="mt-4">
        <img src="<?php echo '../neuraphp/storage/' . basename($file); ?>" alt="Generated" class="rounded shadow max-w-full">
        <div class="text-xs text-gray-500 mt-1">Imagem gerada pelo provedor selecionado.</div>
      </div>
    <?php endif; ?>
    <?php if (!empty($debug)): ?>
      <div class="bg-gray-900 text-green-200 text-xs mt-6 p-4 rounded">
        <div class="font-bold text-green-400 mb-1">Debug Info (API Response)</div>
        <pre><?php echo htmlspecialchars(json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
      </div>
    <?php endif; ?>
    <a href="index.php" class="block mt-6 text-blue-500">&larr; All Demos</a>
  </div>
</body>
</html>
