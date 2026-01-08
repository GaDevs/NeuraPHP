<?php
// /demos/_provider.php - Shared provider/session logic for all demos
session_start();

// Helper: check if provider/key is set and valid
function demoProviderReady() {
    return !empty($_SESSION['provider']) && !empty($_SESSION['api_key']);
}

$providers = [
    'openai' => 'OpenAI',
    'gemini' => 'Gemini',
    'claude' => 'Claude',
];

// Handle provider/key/model form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['provider_select'])) {
    $sel = strtolower(trim($_POST['provider'] ?? ''));
    $key = trim($_POST['api_key'] ?? '');
    $model = isset($_POST['model']) ? trim($_POST['model']) : null;
    $imageModel = isset($_POST['image_model']) ? trim($_POST['image_model']) : null;
    $voiceModel = isset($_POST['voice_model']) ? trim($_POST['voice_model']) : null;
    $embeddingsModel = isset($_POST['embeddings_model']) ? trim($_POST['embeddings_model']) : null;
    if (isset($providers[$sel]) && $key !== '') {
        $_SESSION['provider'] = $sel;
        $_SESSION['api_key'] = $key;
        if ($model) {
            $_SESSION['model'] = $model;
        } else {
            unset($_SESSION['model']);
        }
        if ($imageModel) {
            $_SESSION['image_model'] = $imageModel;
        } else {
            unset($_SESSION['image_model']);
        }
        if ($voiceModel) {
            $_SESSION['voice_model'] = $voiceModel;
        } else {
            unset($_SESSION['voice_model']);
        }
        if ($embeddingsModel) {
            $_SESSION['embeddings_model'] = $embeddingsModel;
        } else {
            unset($_SESSION['embeddings_model']);
        }
    }
}

// Helper: get provider instance
function getSelectedProvider() {
    global $providers;
    if (empty($_SESSION['provider']) || empty($_SESSION['api_key'])) {
        return null;
    }
    $sel = $_SESSION['provider'];
    $key = $_SESSION['api_key'];
    $class = 'NeuraPHP\\Providers\\' . $providers[$sel];
    if (!class_exists($class)) {
        require_once __DIR__ . '/../neuraphp/providers/' . $providers[$sel] . '.php';
    }
    return new $class($key);
}

// Helper: render provider selection form and model selectors
function renderProviderForm() {
    global $providers;
    $sessionData = getFormSessionData();
    $modelsConfig = include_once __DIR__ . '/../neuraphp/config/models.php';
    
    echo '<form method="post" class="flex flex-col md:flex-row gap-2 mb-4 items-end">';
    renderProviderSelect($providers, $sessionData['sel']);
    renderApiKeyInput($sessionData['key']);
    
    if ($sessionData['sel'] && isset($modelsConfig[$sessionData['sel']])) {
        renderModelSelectors($modelsConfig[$sessionData['sel']], $sessionData);
    }
    
    echo '<button name="provider_select" value="1" class="bg-gray-700 text-white px-4 py-2 rounded">Set</button>';
    echo '</form>';
    echo '<div class="text-xs text-gray-500 mb-4">Your API key is used only for this session and never stored.</div>';
}

function getFormSessionData(): array {
    return [
        'sel' => $_SESSION['provider'] ?? '',
        'key' => $_SESSION['api_key'] ?? '',
        'model' => $_SESSION['model'] ?? '',
        'image_model' => $_SESSION['image_model'] ?? '',
        'voice_model' => $_SESSION['voice_model'] ?? '',
        'embeddings_model' => $_SESSION['embeddings_model'] ?? ''
    ];
}

function renderProviderSelect(array $providers, string $selected): void {
    echo '<div><label class="block text-xs font-semibold mb-1">Provider</label><select name="provider" class="border rounded p-2" required onchange="this.form.submit()">';
    foreach ($providers as $k => $v) {
        $s = ($selected === $k) ? 'selected' : '';
        echo "<option value='$k' $s>$v</option>";
    }
    echo '</select></div>';
}

function renderApiKeyInput(string $key): void {
    echo '<div><label class="block text-xs font-semibold mb-1">API Key</label>';
    echo '<input name="api_key" type="password" class="border rounded p-2" value="' . htmlspecialchars($key) . '" required></div>';
}

function renderModelSelectors(array $providerConfig, array $sessionData): void {
    renderModelSelector('Modelo Chat', 'model', $providerConfig['chat_models'] ?? [], $sessionData['model']);
    renderModelSelector('Modelo Imagem', 'image_model', $providerConfig['image_models'] ?? [], $sessionData['image_model']);
    renderModelSelector('Modelo Voz', 'voice_model', $providerConfig['voice_models'] ?? [], $sessionData['voice_model']);
    renderModelSelector('Modelo Embeddings', 'embeddings_model', $providerConfig['embeddings_models'] ?? [], $sessionData['embeddings_model']);
}

function renderModelSelector(string $label, string $name, array $options, string $selected): void {
    if (empty($options)) {
        return;
    }
    echo '<div><label class="block text-xs font-semibold mb-1">' . htmlspecialchars($label) . '</label>';
    echo '<select name="' . htmlspecialchars($name) . '" class="border rounded p-2">';
    foreach ($options as $id => $desc) {
        $s = ($selected === $id) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($id) . "' $s>" . htmlspecialchars($desc) . "</option>";
    }
    echo '</select></div>';
}
