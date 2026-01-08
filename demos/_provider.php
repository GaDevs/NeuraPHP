<?php
// /demos/_provider.php - Shared provider/session logic for all demos
session_start();

$providers = [
    'openai' => 'OpenAI',
    'gemini' => 'Gemini',
    'claude' => 'Claude',
];

// Handle provider/key form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['provider_select'])) {
    $sel = strtolower(trim($_POST['provider'] ?? ''));
    $key = trim($_POST['api_key'] ?? '');
    if (isset($providers[$sel]) && $key !== '') {
        $_SESSION['provider'] = $sel;
        $_SESSION['api_key'] = $key;
    }
}

// Helper: get provider instance
function get_selected_provider() {
    global $providers;
    if (empty($_SESSION['provider']) || empty($_SESSION['api_key'])) return null;
    $sel = $_SESSION['provider'];
    $key = $_SESSION['api_key'];
    $class = 'NeuraPHP\\Providers\\' . $providers[$sel];
    if (!class_exists($class)) {
        require_once __DIR__ . '/../neuraphp/providers/' . $providers[$sel] . '.php';
    }
    return new $class($key);
}

// Helper: check if provider/key is set
function demo_provider_ready() {
    return !empty($_SESSION['provider']) && !empty($_SESSION['api_key']);
}

// Helper: render provider/key form
function render_provider_form() {
    global $providers;
    $sel = $_SESSION['provider'] ?? '';
    $key = $_SESSION['api_key'] ?? '';
    echo '<form method="post" class="flex flex-col md:flex-row gap-2 mb-4 items-end">';
    echo '<div><label class="block text-xs font-semibold mb-1">Provider</label><select name="provider" class="border rounded p-2" required>';
    foreach ($providers as $k => $v) {
        $s = ($sel === $k) ? 'selected' : '';
        echo "<option value='$k' $s>$v</option>";
    }
    echo '</select></div>';
    echo '<div><label class="block text-xs font-semibold mb-1">API Key</label><input name="api_key" type="password" class="border rounded p-2" value="' . htmlspecialchars($key) . '" required></div>';
    echo '<button name="provider_select" value="1" class="bg-gray-700 text-white px-4 py-2 rounded">Set</button>';
    echo '</form>';
    echo '<div class="text-xs text-gray-500 mb-4">Your API key is used only for this session and never stored.</div>';
}
