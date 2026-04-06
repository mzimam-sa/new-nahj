<?php
// Advanced health check - bypasses Laravel for speed
// Used by Railway, load balancers, and monitoring

$start = microtime(true);
$checks = [];
$healthy = true;

// 1. PHP Check
$checks['php'] = [
    'status' => 'ok',
    'version' => phpversion(),
];

// 2. Database Check
try {
    $dbHost = getenv('DB_HOST') ?: 'localhost';
    $dbPort = getenv('DB_PORT') ?: '5432';
    $dbName = getenv('DB_DATABASE') ?: 'railway';
    $dbUser = getenv('DB_USERNAME') ?: 'postgres';
    $dbPass = getenv('DB_PASSWORD') ?: '';
    $dbConn = getenv('DB_CONNECTION') ?: 'pgsql';

    $dsn = $dbConn === 'mysql'
        ? "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}"
        : "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

    $dbStart = microtime(true);
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_TIMEOUT => 5]);
    $pdo->query('SELECT 1');
    $dbTime = round((microtime(true) - $dbStart) * 1000, 2);

    $checks['database'] = [
        'status' => 'ok',
        'driver' => $dbConn,
        'response_ms' => $dbTime,
    ];
} catch (Exception $e) {
    $healthy = false;
    $checks['database'] = [
        'status' => 'error',
        'message' => $e->getMessage(),
    ];
}

// 3. Storage Check
$storagePath = __DIR__ . '/../storage/logs';
$storageWritable = is_writable($storagePath);
$checks['storage'] = [
    'status' => $storageWritable ? 'ok' : 'error',
    'writable' => $storageWritable,
];
if (!$storageWritable) $healthy = false;

// 4. Cache Directory Check
$cachePath = __DIR__ . '/../storage/framework/cache/data';
$cacheWritable = is_dir($cachePath) && is_writable($cachePath);
$checks['cache'] = [
    'status' => $cacheWritable ? 'ok' : 'error',
    'writable' => $cacheWritable,
];

// 5. OPcache Check
$opcacheEnabled = function_exists('opcache_get_status') && @opcache_get_status() !== false;
$checks['opcache'] = [
    'status' => $opcacheEnabled ? 'ok' : 'disabled',
    'enabled' => $opcacheEnabled,
];
if ($opcacheEnabled) {
    $opcacheStatus = opcache_get_status();
    $checks['opcache']['memory_used_mb'] = round($opcacheStatus['memory_usage']['used_memory'] / 1024 / 1024, 2);
    $checks['opcache']['hit_rate'] = round($opcacheStatus['opcache_statistics']['opcache_hit_rate'] ?? 0, 2) . '%';
    $checks['opcache']['cached_scripts'] = $opcacheStatus['opcache_statistics']['num_cached_scripts'] ?? 0;
}

// 6. Disk Space
$freeSpace = @disk_free_space('/');
$totalSpace = @disk_total_space('/');
if ($freeSpace && $totalSpace) {
    $usedPercent = round((1 - $freeSpace / $totalSpace) * 100, 1);
    $checks['disk'] = [
        'status' => $usedPercent < 90 ? 'ok' : 'warning',
        'used_percent' => $usedPercent . '%',
        'free_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
    ];
}

// 7. Memory
$checks['memory'] = [
    'limit' => ini_get('memory_limit'),
    'used_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
];

// Response
$totalTime = round((microtime(true) - $start) * 1000, 2);

http_response_code($healthy ? 200 : 503);
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo json_encode([
    'status' => $healthy ? 'healthy' : 'unhealthy',
    'timestamp' => date('c'),
    'response_ms' => $totalTime,
    'checks' => $checks,
], JSON_PRETTY_PRINT);
