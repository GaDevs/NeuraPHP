<?php
// Constants for repeated strings
const ERR_METHOD_NOT_ALLOWED = 'Method Not Allowed';
const PHP_INPUT = 'php://input';
const STORAGE_DIR = __DIR__ . '/../storage';
const ERR_TOPIC_REQUIRED = 'topic required';
/**
 * NeuraPHP AI Engine™
 * Base REST API
 *
 * @package NeuraPHP\Rest
 * @author  NeuraPHP Team
 * @license MIT
 */


use NeuraPHP\Core\AI;
use NeuraPHP\Core\Provider;
use NeuraPHP\Core\Cache;
use NeuraPHP\Core\RateLimit;
use NeuraPHP\Core\Memory;
use NeuraPHP\Modules\Chat;
use NeuraPHP\Modules\Image;
use NeuraPHP\Modules\Voice;
use NeuraPHP\Modules\SEO;
use NeuraPHP\Modules\Video;
use NeuraPHP\Modules\Automations;



// Autoload classes using Composer or a PSR-4 compatible autoloader
// require __DIR__ . '/../../vendor/autoload.php';
// If not using Composer, manually require class files as needed.

header('Content-Type: application/json');



$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = $_GET['path'] ?? '/';

// Permitir POST direto no endpoint raiz para testes
if ($path === '/' && $method === 'POST') {
    $input = json_decode(file_get_contents(PHP_INPUT), true);
    $prompt = $input['prompt'] ?? null;
    echo json_encode([
        'status' => 'ok',
        'echo' => $prompt,
        'engine' => 'NeuraPHP AI Engine™',
        'version' => '1.0.0',
        'time' => date('c')
    ]);
    exit;
}

// Simple routing
switch ($path) {
    case '/':
        echo json_encode([
            'status' => 'ok',
            'engine' => 'NeuraPHP AI Engine™',
            'version' => '1.0.0',
            'time' => date('c')
        ]);
        break;
    case '/status':
        echo json_encode([
            'status' => 'ok',
            'uptime' => @file_exists('/proc/uptime') ? file_get_contents('/proc/uptime') : null,
            'php_version' => PHP_VERSION
        ]);
        break;
    case '/chat':
        // Persistent chat endpoint
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $conversationId = $input['conversation_id'] ?? null;
        $role = $input['role'] ?? 'user';
        $message = $input['message'] ?? null;
        if (!$conversationId || !$message) {
            http_response_code(400);
            echo json_encode(['error' => 'conversation_id and message required']);
            break;
        }
        $memory = new Memory();
        $chat = new Chat($memory);
        $chat->addMessage($conversationId, $role, $message);
        $history = $chat->getHistory($conversationId);
        echo json_encode([
            'status' => 'ok',
            'conversation_id' => $conversationId,
            'history' => $history
        ]);
        break;
    case '/image':
        // Image generation endpoint
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $prompt = $input['prompt'] ?? null;
        if (!$prompt) {
            http_response_code(400);
            echo json_encode(['error' => 'prompt required']);
            break;
        }
        $imageModule = new Image();
        $file = $imageModule->generate($prompt, STORAGE_DIR);
        echo json_encode([
            'status' => 'ok',
            'file' => basename($file),
            'path' => $file
        ]);
        break;
    case '/voice':
        // Voice generation endpoint
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $text = $input['text'] ?? null;
        if (!$text) {
            http_response_code(400);
            echo json_encode(['error' => 'text required']);
            break;
        }
        $voiceModule = new Voice();
        $file = $voiceModule->generate($text, STORAGE_DIR);
        echo json_encode([
            'status' => 'ok',
            'file' => basename($file),
            'path' => $file
        ]);
        break;
    case '/seo/article':
        // SEO article generation endpoint
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $topic = $input['topic'] ?? null;
        if (!$topic) {
            http_response_code(400);
            echo json_encode(['error' => ERR_TOPIC_REQUIRED]);
            break;
        }
        $seo = new SEO();
        $article = $seo->generateArticle($topic);
        echo json_encode([
            'status' => 'ok',
            'article' => $article
        ]);
        break;
    case '/seo/meta':
        // SEO meta generation endpoint
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $topic = $input['topic'] ?? null;
        if (!$topic) {
            http_response_code(400);
            echo json_encode(['error' => 'topic required']);
            break;
        }
        $seo = new SEO();
        $meta = $seo->generateMeta($topic);
        echo json_encode([
            'status' => 'ok',
            'meta' => $meta
        ]);
        break;
    case '/video':
        // Video/shorts generation endpoint
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $topic = $input['topic'] ?? null;
        if (!$topic) {
            http_response_code(400);
            echo json_encode(['error' => 'topic required']);
            break;
        }
        $video = new Video();
        $result = $video->generateShort($topic, STORAGE_DIR);
        echo json_encode([
            'status' => 'ok',
            'file' => basename($result['file']),
            'slides' => $result['slides'],
            'narration' => $result['narration']
        ]);
        break;
    case '/automation/workflows':
        // List all workflows
        $automations = new Automations();
        // For demo, register a sample workflow
        $automations->registerWorkflow('sample', ['steps' => ['step1', 'step2']]);
        echo json_encode([
            'status' => 'ok',
            'workflows' => $automations->listWorkflows()
        ]);
        break;
    case '/automation/trigger':
        // Trigger a workflow
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $name = $input['name'] ?? null;
        $payload = $input['payload'] ?? [];
        $automations = new Automations();
        $automations->registerWorkflow('sample', ['steps' => ['step1', 'step2']]);
        if (!$name) {
            http_response_code(400);
            echo json_encode(['error' => 'name required']);
            break;
        }
        $result = $automations->trigger($name, $payload);
        echo json_encode([
            'status' => 'ok',
            'result' => $result
        ]);
        break;
    case '/automation/webhook':
        // Handle webhook
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $event = $input['event'] ?? null;
        $data = $input['data'] ?? [];
        $automations = new Automations();
        if (!$event) {
            http_response_code(400);
            echo json_encode(['error' => 'event required']);
            break;
        }
        $result = $automations->webhook($event, $data);
        echo json_encode([
            'status' => 'ok',
            'result' => $result
        ]);
        break;
    case '/automation/job':
        // Add a job
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => ERR_METHOD_NOT_ALLOWED]);
            break;
        }
        $input = json_decode(file_get_contents(PHP_INPUT), true);
        $job = $input['job'] ?? null;
        $params = $input['params'] ?? [];
        $automations = new Automations();
        if (!$job) {
            http_response_code(400);
            echo json_encode(['error' => 'job required']);
            break;
        }
        $result = $automations->addJob($job, $params);
        echo json_encode([
            'status' => 'ok',
            'result' => $result
        ]);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
}
