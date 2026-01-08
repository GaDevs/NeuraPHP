<?php
/**
 * NeuraPHP AI Engine™
 * Installer (Stage 1)
 *
 * @package NeuraPHP
 * @author  NeuraPHP Team
 * @license MIT
 */

$dirs = [
    __DIR__ . '/cache',
    __DIR__ . '/rate',
    __DIR__ . '/logs',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        echo "Created directory: $dir\n";
    }
}
echo "NeuraPHP AI Engine™ core folders initialized.\n";
