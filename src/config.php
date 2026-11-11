<?php
declare(strict_types=1);
session_start();

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $url = getenv('DATABASE_URL');
        if ($url) { // formato: postgres://user:pass@host:port/dbname
            $p = parse_url($url);
            $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s;sslmode=require',
                $p['host'], $p['port'] ?? 5432, ltrim($p['path'], '/'));
            $user = urldecode($p['user'] ?? '');
            $pass = urldecode($p['pass'] ?? '');
        } else {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s',
                getenv('DB_HOST') ?: 'localhost',
                getenv('DB_PORT') ?: '5432',
                getenv('DB_NAME') ?: 'amazon_cart');
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: 'postgres';
        }
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function money($v): string { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }

function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }

function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Token CSRF inválido.');
    }
}

function cart_count(): int { return (int)array_sum($_SESSION['cart'] ?? []); }

/** Retorna os itens do carrinho com dados atuais do banco. */
function cart_items(): array
{
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_map('intval', array_keys($cart));
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = db()->prepare("SELECT * FROM products WHERE id IN ($in) ORDER BY name");
    $st->execute($ids);
    $items = [];
    foreach ($st as $p) {
        $p['qty']      = max(1, min((int)$cart[$p['id']], (int)$p['stock']));
        $p['subtotal'] = $p['qty'] * (float)$p['price'];
        $items[] = $p;
    }
    return $items;
}

function cart_total(array $items): float { return array_sum(array_column($items, 'subtotal')); }
