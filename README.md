# NeuraPHP AI Engine™

Universal AI Infrastructure SDK for PHP Applications

## Installation

1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd NeuraPHP
   ```
2. **Install dependencies:**
   ```bash
   composer install
   ```
3. **Initialize folders:**
   ```bash
   php neuraphp/install.php
   ```

## Usage

- Integrate the SDK in your PHP app:
  ```php
  use NeuraPHP\Core\AI;
  $ai = AI::getInstance(require 'neuraphp/config.php');
  ```
- Use REST API endpoints via `neuraphp/rest/api.php?path=...`

## API Examples

- **Status:**
  ```http
  GET /neuraphp/rest/api.php?path=/
  ```
- **Chat:**
  ```http
  POST /neuraphp/rest/api.php?path=/chat
  { "conversation_id": "abc", "role": "user", "message": "Hello" }
  ```
- **Image Generation:**
  ```http
  POST /neuraphp/rest/api.php?path=/image
  { "prompt": "A cat on Mars" }
  ```
- **Voice Generation:**
  ```http
  POST /neuraphp/rest/api.php?path=/voice
  { "text": "Hello world" }
  ```
- **SEO Article:**
  ```http
  POST /neuraphp/rest/api.php?path=/seo/article
  { "topic": "php" }
  ```
- **Video/Shorts:**
  ```http
  POST /neuraphp/rest/api.php?path=/video
  { "topic": "php" }
  ```
- **Automations:**
  ```http
  GET /neuraphp/rest/api.php?path=/automation/workflows
  POST /neuraphp/rest/api.php?path=/automation/trigger
  { "name": "sample", "payload": {} }
  ```
- **Local LLM:**
  ```http
  POST /neuraphp/rest/api.php?path=/local-llm
  { "prompt": "What is PHP?" }
  ```

## Environment Setup

- PHP 8.0 or higher
- Composer
- Web server (Apache, Nginx, or PHP built-in)
- Writable folders: `neuraphp/cache`, `neuraphp/rate`, `neuraphp/memory`, `neuraphp/storage`

## Testing

- Run all tests:
  ```bash
  composer test
  ```

## WordPress Plugin

- Copy `neuraphp/plugins/wordpress/neuraphp.php` to your WordPress plugins directory and activate.
- Access `/wp-json/neuraphp/v1/status` for engine status.

---

© 2026 NeuraPHP Team
