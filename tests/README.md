# NeuraPHP AI Engine™ Test Suite

## Running Tests

1. Install dependencies:
   ```bash
   composer install
   ```
2. Run all tests:
   ```bash
   ./vendor/bin/phpunit --testdox
   ```

## Test Structure
- **/tests/CoreTest.php, /tests/ChatTest.php, etc.**: Unit tests for all modules
- **/tests/Integration/**: ProviderFactory, ModelRegistry, REST endpoint integration
- **/tests/Security/**: XSS, API key, file traversal, session, rate limit
- **/tests/Performance/**: Cache, rate, memory stress

## Security Notes
- No API keys are logged or stored
- All user input is sanitized
- Session IDs are regenerated on login
- Rate limiting is enforced per IP

## Performance Benchmarks
- Cache: 1000 ops/sec+ (local disk)
- RateLimit: 10+ ops/sec/IP
- Memory: 100+ conversations/sec

## Coverage & Compliance
- PSR-12, PHP 8.1+
- 90%+ code coverage (target)
- All critical security tests pass

---

For full QA report, see FINAL_REPORT.md
