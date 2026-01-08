<?php
// NeuraPHP AI Engine™ Chat Demo (Provider-aware)

require_once __DIR__ . '/../vendor/autoload.php';
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
  require_once __DIR__ . '/../sdk/vendor/autoload.php';
}
require_once __DIR__ . '/_provider.php';

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
if (demoProviderReady()) {
  $providerKey = $_SESSION['provider'];
  $apiKey = $_SESSION['api_key'];
  $chatModel = $_SESSION['model'] ?? null;
  $providersFile = __DIR__ . '/../neuraphp/config/providers.php';
  $modelsFile = __DIR__ . '/../neuraphp/config/models.php';
  $factory = ProviderFactory::fromConfig($providersFile, $modelsFile);
  $provider = $factory->create($providerKey, $apiKey, $chatModel);
}
$chat = new Chat($memory, $provider);

$debug = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['provider_select']) && $allowed && demoProviderReady()) {
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
  <title>Chat - NeuraPHP</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">💬 NeuraPHP Chat</h1>
    
    <?php renderProviderForm(); ?>

    <?php if (!demoProviderReady()): ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
      <p class="font-bold">Setup Required</p>
      <p>Select a provider and enter your API key above to start chatting.</p>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="border rounded p-4 h-64 overflow-y-auto mb-4 bg-gray-50">
        <?php foreach ($history as $h): ?>
          <div class="mb-3 text-sm <?php echo ($h['role'] === 'user') ? 'text-right' : 'text-left'; ?>">
            <span class="inline-block bg-<?php echo ($h['role'] === 'user') ? 'blue' : 'gray'; ?>-200 px-3 py-2 rounded max-w-xs break-words">
              <strong><?php echo ucfirst($h['role']); ?>:</strong> <?php echo htmlspecialchars($h['content']); ?>
            </span>
          </div>
        <?php endforeach; ?>
        <?php if ($msg): ?>
          <div class="mb-3 text-sm text-left">
            <span class="inline-block bg-green-200 px-3 py-2 rounded max-w-xs break-words">
              <?php echo htmlspecialchars($msg); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>

      <form method="POST" class="flex gap-2">
        <input
          name="message"
          type="text"
          required
          maxlength="500"
          class="flex-1 p-3 border rounded"
          placeholder="Type your message..."
          <?php if (!$allowed || !demoProviderReady()) { echo 'disabled'; } ?>
        >
        <button
          class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700"
          <?php if (!$allowed || !demoProviderReady()) { echo 'disabled'; } ?>
        >
          Send
        </button>
      </form>
    </div>

    <?php if ($debug): ?>
      <div class="mt-6 bg-gray-800 text-white p-4 rounded font-mono text-xs overflow-auto max-h-64">
        <h3 class="font-bold mb-2">Debug Output</h3>
        <pre><?php echo htmlspecialchars(json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
