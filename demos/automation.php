<?php
// NeuraPHP AI Engine™ Automations Demo (Provider-aware)
require_once __DIR__ . '/_provider.php';
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../neuraphp/modules/Automations.php';
require_once __DIR__ . '/../neuraphp/core/RateLimit.php';
require_once __DIR__ . '/../neuraphp/core/ProviderFactory.php';
require_once __DIR__ . '/../neuraphp/core/ModelRegistry.php';
require_once __DIR__ . '/../neuraphp/core/ProviderInterface.php';
use NeuraPHP\Modules\Automations;
use NeuraPHP\Core\RateLimit;
use NeuraPHP\Core\ProviderFactory;

$rate = new RateLimit(__DIR__ . '/../neuraphp/rate');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
$allowed = $rate->check('automation_demo_' . $ip, 10, 60);

$msg = '';
$result = null;

$provider = null;
if (demo_provider_ready()) {
  $providerKey = $_SESSION['provider'];
  $apiKey = $_SESSION['api_key'];
  $providersFile = __DIR__ . '/../neuraphp/config/providers.php';
  $modelsFile = __DIR__ . '/../neuraphp/config/models.php';
  $factory = ProviderFactory::fromConfig($providersFile, $modelsFile);
  $provider = $factory->create($providerKey, $apiKey);
}
$auto = new Automations($provider);
$auto->registerWorkflow('sample', ['steps' => ['step1', 'step2']]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['provider_select']) && $allowed && demo_provider_ready()) {
  $action = $_POST['action'] ?? '';
  if ($action === 'trigger') {
    $result = $auto->trigger('sample', ['demo' => 'yes']);
    $msg = 'Workflow triggered!';
  } elseif ($action === 'job') {
    $result = $auto->addJob('demo_job', ['foo' => 'bar']);
    $msg = 'Job added!';
  } elseif ($action === 'webhook') {
    $result = $auto->webhook('demo_event', ['bar' => 'baz']);
    $msg = 'Webhook received!';
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Automations Demo | NeuraPHP</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-lg mx-auto py-10">
    <h1 class="text-2xl font-bold mb-2">🤖 Automations Demo</h1>
    <p class="mb-4 text-gray-600">Trigger workflows, add jobs, and send webhooks. Rate limited for safety.</p>
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
      <button name="action" value="trigger" class="bg-blue-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) echo 'disabled'; ?>>Trigger Workflow</button>
      <button name="action" value="job" class="bg-green-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) echo 'disabled'; ?>>Add Job</button>
      <button name="action" value="webhook" class="bg-purple-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) echo 'disabled'; ?>>Send Webhook</button>
    </form>
    <?php if ($result): ?>
      <div class="bg-white rounded shadow p-4 mt-4">
        <pre class="text-xs text-gray-700"><?php echo htmlspecialchars(print_r($result, true)); ?></pre>
      </div>
    <?php endif; ?>
    <a href="index.php" class="block mt-6 text-blue-500">&larr; All Demos</a>
  </div>
</body>
</html>
