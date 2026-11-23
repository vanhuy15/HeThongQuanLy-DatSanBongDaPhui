<?php
// Unit Test Bootstrap for PitchManager

require_once __DIR__ . '/phpunit_stubs.php';

define('ROOT_DIR', dirname(__DIR__, 2));

// Load .env if exists
if (file_exists(ROOT_DIR . '/.env')) {
    $lines = file(ROOT_DIR . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1], " \t\n\r\0\x0B\"'");
            $_ENV[$key] = $val;
            putenv("$key=$val");
        }
    }
}

// Autoloader for Models, Controllers, Services, Config, Core
spl_autoload_register(function ($class) {
    $prefixes = [
        'Models\\' => ROOT_DIR . '/models/',
        'Controllers\\' => ROOT_DIR . '/controllers/',
        'Services\\' => ROOT_DIR . '/services/',
        'Config\\' => ROOT_DIR . '/config/',
        'Core\\' => ROOT_DIR . '/core/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

    // Direct class search without namespace
    $folders = ['models', 'controllers', 'services', 'config', 'core'];
    foreach ($folders as $folder) {
        $file = ROOT_DIR . '/' . $folder . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Require common helpers
if (file_exists(ROOT_DIR . '/includes/csrf.php')) {
    require_once ROOT_DIR . '/includes/csrf.php';
}
