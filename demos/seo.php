<?php
// NeuraPHP AI Engine™ SEO & Content Demo (Provider-aware)
require_once __DIR__ . '/_provider.php';
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../neuraphp/modules/SEO.php';
require_once __DIR__ . '/../neuraphp/core/RateLimit.php';
require_once __DIR__ . '/../neuraphp/core/ProviderFactory.php';
require_once __DIR__ . '/../neuraphp/core/ModelRegistry.php';
require_once __DIR__ . '/../neuraphp/core/ProviderInterface.php';
use NeuraPHP\Modules\SEO;
use NeuraPHP\Core\RateLimit;
use NeuraPHP\Core\ProviderFactory;

$rate = new RateLimit(__DIR__ . '/../neuraphp/rate');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
$allowed = $rate->check('seo_demo_' . $ip, 10, 60);

$msg = '';
$article = null;
$meta = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['provider_select']) && $allowed && demo_provider_ready()) {
  $topic = trim(filter_input(INPUT_POST, 'topic', FILTER_DEFAULT));
  $action = $_POST['action'] ?? '';
  $providerKey = $_SESSION['provider'];
  $apiKey = $_SESSION['api_key'];
  $providersFile = __DIR__ . '/../neuraphp/config/providers.php';
  $modelsFile = __DIR__ . '/../neuraphp/config/models.php';
  $factory = ProviderFactory::fromConfig($providersFile, $modelsFile);
  $provider = $factory->create($providerKey, $apiKey);
  $seo = new SEO($provider);
  if ($topic && $action === 'article') {
    $article = $seo->generateArticle(htmlspecialchars($topic, ENT_QUOTES, 'UTF-8'));
    $msg = 'SEO article generated!';
  } elseif ($topic && $action === 'meta') {
    $meta = $seo->generateMeta(htmlspecialchars($topic, ENT_QUOTES, 'UTF-8'));
    $msg = 'SEO meta generated!';
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEO & Content Demo | NeuraPHP</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-lg mx-auto py-10">
    <h1 class="text-2xl font-bold mb-2">📈 SEO & Content Demo</h1>
    <p class="mb-4 text-gray-600">Generate SEO articles, titles, and descriptions. Rate limited for safety.</p>
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
      <input name="topic" required maxlength="100" class="flex-1 p-2 border rounded" placeholder="Enter topic..." <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>
      <button name="action" value="article" class="bg-blue-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>Article</button>
      <button name="action" value="meta" class="bg-green-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>Meta</button>
    </form>
    <?php if ($article): ?>
      <div class="bg-white rounded shadow p-4 mt-4">
        <div class="mb-2"><b>Title:</b> <?php echo htmlspecialchars($article['title']); ?></div>
        <div class="mb-2"><b>Description:</b> <?php echo htmlspecialchars($article['description']); ?></div>
        <div><b>Content:</b> <span class="text-gray-700"><?php echo htmlspecialchars($article['content']); ?></span></div>
      </div>
    <?php elseif ($meta): ?>
      <div class="bg-white rounded shadow p-4 mt-4">
        <div class="mb-2"><b>Title:</b> <?php echo htmlspecialchars($meta['title']); ?></div>
        <div><b>Description:</b> <?php echo htmlspecialchars($meta['description']); ?></div>
      </div>
    <?php endif; ?>
    <a href="index.php" class="block mt-6 text-blue-500">&larr; All Demos</a>
  </div>
</body>
</html>
