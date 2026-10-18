<?php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('pgsql:host=%s;port=5432;dbname=%s;sslmode=require', DB_HOST, DB_NAME);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function bool_txt($v): string { return ($v === true || $v === 't' || $v === 1 || $v === '1') ? 'TRUE' : 'FALSE'; }
function redirect(string $to, string $msg = ''): void {
    header('Location: ' . $to . ($msg ? '?msg=' . urlencode($msg) : ''));
    exit;
}
