<?php
// NeuraPHP AI Engine™ Chat Demo (Provider-aware)

require_once __DIR__ . '/../vendor/autoload.php';
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
  require_once __DIR__ . '/../sdk/vendor/autoload.php';
}
require_once __DIR__ . '/_provider.php';
require_once __DIR__ . '/../neuraphp/core/Memory.php';
require_once __DIR__ . '/../neuraphp/modules/Chat.php';
require_once __DIR__ . '/../neuraphp/core/RateLimit.php';
require_once __DIR__ . '/../neuraphp/core/ProviderFactory.php';
require_once __DIR__ . '/../neuraphp/core/ModelRegistry.php';
require_once __DIR__ . '/../neuraphp/core/ProviderInterface.php';
use NeuraPHP\Core\Memory;
use NeuraPHP\Modules\Chat;
use NeuraPHP\Core\RateLimit;
use NeuraPHP\Core\ProviderFactory;

$rate = new RateLimit(__DIR__ . '/../neuraphp/rate');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
$allowed = $rate->check('chat_demo_' . $ip, 10, 60);

$msg = '';
$history = [];
$cid = 'demo_' . md5($ip);

$memory = new Memory(__DIR__ . '/../neuraphp/memory');
$provider = null;
if (demo_provider_ready()) {
  $providerKey = $_SESSION['provider'];
  $apiKey = $_SESSION['api_key'];
  $providersFile = __DIR__ . '/../neuraphp/config/providers.php';
  $modelsFile = __DIR__ . '/../neuraphp/config/models.php';
  $factory = ProviderFactory::fromConfig($providersFile, $modelsFile);
  $provider = $factory->create($providerKey, $apiKey);
}
$chat = new Chat($memory, $provider);

$debug = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['provider_select']) && $allowed && demo_provider_ready()) {
  $role = 'user';
  $input = trim(filter_input(INPUT_POST, 'message', FILTER_DEFAULT));
  if ($input) {
    $chat->addMessage($cid, $role, htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    // Get AI response from provider if available
    $aiMsg = $chat->getAIResponse($cid);
    if ($aiMsg) {
      $msg = 'AI: ' . htmlspecialchars($aiMsg);
      $debug['ai_response'] = $aiMsg;
    } else {
      $msg = 'Message sent!';
      $debug['ai_response'] = null;
    }
    // Debug: capturar última resposta bruta do provider, se possível
    if (isset($provider) && method_exists($provider, 'getLastRawResponse')) {
      $debug['provider_raw'] = $provider->getLastRawResponse();
    }
  }
}
$history = $chat->getHistory($cid);
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat Demo | NeuraPHP</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-lg mx-auto py-10">
    <h1 class="text-2xl font-bold mb-2">💬 Chat Demo</h1>
    <p class="mb-4 text-gray-600">Persistent chat with memory. Each visitor has a unique conversation. Rate limited for safety.</p>
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
      <input name="message" required maxlength="200" class="flex-1 p-2 border rounded" placeholder="Type your message..." <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>
      <button class="bg-blue-600 text-white px-4 py-2 rounded" <?php if(!$allowed || !demo_provider_ready()) { echo 'disabled'; } ?>>Send</button>
    </form>
    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-2">Conversation History</h2>
      <div class="space-y-2 text-sm">
        <?php foreach ($history as $msg): ?>
          <div class="border-b pb-1"><b><?php echo htmlspecialchars($msg['role']); ?>:</b> <?php echo htmlspecialchars($msg['message']); ?> <span class="text-gray-400 text-xs"><?php echo date('H:i', $msg['timestamp']); ?></span></div>
        <?php endforeach; ?>
        <?php if (empty($history)): ?>
          <div class="text-gray-400">No messages yet.</div>
        <?php endif; ?>
      </div>
    </div>
    <?php if (!empty($debug)): ?>
      <div class="bg-gray-900 text-green-200 text-xs mt-6 p-4 rounded">
        <div class="font-bold text-green-400 mb-1">Debug Info (dev)</div>
        <pre><?php echo htmlspecialchars(json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
      </div>
    <?php endif; ?>
    <a href="index.php" class="block mt-6 text-blue-500">&larr; All Demos</a>
  </div>
</body>
</html>
