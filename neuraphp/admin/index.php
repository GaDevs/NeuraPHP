<?php
/**
 * NeuraPHP AI Engine™
 * Minimal Admin Panel (Stage 1)
 *
 * @package NeuraPHP\Admin
 * @author  NeuraPHP Team
 * @license MIT
 */

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NeuraPHP Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7fa; color: #222; margin: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        h1 { font-size: 2em; margin-bottom: 0.5em; }
        .status { margin: 1em 0; padding: 1em; background: #e3f7e3; border-radius: 4px; color: #1a7f1a; }
        .info { font-size: 1em; color: #555; }
        .footer { margin-top: 2em; font-size: 0.9em; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>NeuraPHP Admin</h1>
        <div class="status">Engine Status: <b>Online</b></div>
        <div class="info">
            <b>Version:</b> 1.0.0<br>
            <b>PHP:</b> <?php echo PHP_VERSION; ?><br>
            <b>Time:</b> <?php echo date('c'); ?><br>
        </div>
        <div class="footer">
            &copy; <?php echo date('Y'); ?> NeuraPHP AI Engine™
        </div>
    </div>
</body>
</html>
