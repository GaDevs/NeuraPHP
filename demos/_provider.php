// Helper: check if provider/key is set and valid
function demo_provider_ready() {
    return !empty($_SESSION['provider']) && !empty($_SESSION['api_key']);
}
<?php
// /demos/_provider.php - Shared provider/session logic for all demos
session_start();

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
function render_provider_form() {
    global $providers;
    $sel = $_SESSION['provider'] ?? '';
    $key = $_SESSION['api_key'] ?? '';
    $model = $_SESSION['model'] ?? '';
    $imageModel = $_SESSION['image_model'] ?? '';
    $voiceModel = $_SESSION['voice_model'] ?? '';
    $embeddingsModel = $_SESSION['embeddings_model'] ?? '';
    $modelsConfig = include __DIR__ . '/../neuraphp/config/models.php';
    $chatOptions = $sel && isset($modelsConfig[$sel]['chat_models']) ? $modelsConfig[$sel]['chat_models'] : [];
    $imageOptions = $sel && isset($modelsConfig[$sel]['image_models']) ? $modelsConfig[$sel]['image_models'] : [];
    $voiceOptions = $sel && isset($modelsConfig[$sel]['voice_models']) ? $modelsConfig[$sel]['voice_models'] : [];
    $embeddingsOptions = $sel && isset($modelsConfig[$sel]['embeddings_models']) ? $modelsConfig[$sel]['embeddings_models'] : [];
    echo '<form method="post" class="flex flex-col md:flex-row gap-2 mb-4 items-end">';
    echo '<div><label class="block text-xs font-semibold mb-1">Provider</label><select name="provider" class="border rounded p-2" required onchange="this.form.submit()">';
    foreach ($providers as $k => $v) {
        $s = ($sel === $k) ? 'selected' : '';
        echo "<option value='$k' $s>$v</option>";
    }
    echo '</select></div>';
    echo '<div><label class="block text-xs font-semibold mb-1">API Key</label><input name="api_key" type="password" class="border rounded p-2" value="' . htmlspecialchars($key) . '" required></div>';
    if ($chatOptions) {
        $provName = ucfirst($sel);
        echo '<div><label class="block text-xs font-semibold mb-1">Modelo Chat ' . $provName . '</label><select name="model" class="border rounded p-2">';
        foreach ($chatOptions as $id => $desc) {
            $s = ($model === $id) ? 'selected' : '';
            echo "<option value='$id' $s>$desc</option>";
        }
        echo '</select></div>';
    }
    if ($imageOptions) {
        echo '<div><label class="block text-xs font-semibold mb-1">Modelo Imagem</label><select name="image_model" class="border rounded p-2">';
        foreach ($imageOptions as $id => $desc) {
            $s = ($imageModel === $id) ? 'selected' : '';
            echo "<option value='$id' $s>$desc</option>";
        }
        echo '</select></div>';
    }
    if ($voiceOptions) {
        echo '<div><label class="block text-xs font-semibold mb-1">Modelo Voz</label><select name="voice_model" class="border rounded p-2">';
        foreach ($voiceOptions as $id => $desc) {
            $s = ($voiceModel === $id) ? 'selected' : '';
            echo "<option value='$id' $s>$desc</option>";
        }
        echo '</select></div>';
    }
    if ($embeddingsOptions) {
        echo '<div><label class="block text-xs font-semibold mb-1">Modelo Embeddings</label><select name="embeddings_model" class="border rounded p-2">';
        foreach ($embeddingsOptions as $id => $desc) {
            $s = ($embeddingsModel === $id) ? 'selected' : '';
            echo "<option value='$id' $s>$desc</option>";
        }
        echo '</select></div>';
    }
    echo '<button name="provider_select" value="1" class="bg-gray-700 text-white px-4 py-2 rounded">Set</button>';
    echo '</form>';
    echo '<div class="text-xs text-gray-500 mb-4">Your API key is used only for this session and never stored.</div>';
}
