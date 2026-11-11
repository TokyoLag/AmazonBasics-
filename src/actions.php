<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_check();

$action = $_POST['action'] ?? '';
$id     = (int)($_POST['id'] ?? 0);
$back   = 'cart.php';

if ($id > 0) {
    $st = db()->prepare('SELECT stock FROM products WHERE id = ?');
    $st->execute([$id]);
    $stock = $st->fetchColumn();

    if ($stock !== false) {
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];

        switch ($action) {
            case 'add':
                $_SESSION['cart'][$id] = min(($_SESSION['cart'][$id] ?? 0) + 1, (int)$stock);
                $b = $_POST['back'] ?? '';
                // só aceita redirecionamento relativo (evita open redirect)
                $back = (str_starts_with($b, '/') && !str_starts_with($b, '//')) ? $b : 'index.php';
                break;
            case 'update':
                $qty = (int)($_POST['qty'] ?? 1);
                if ($qty <= 0) unset($_SESSION['cart'][$id]);
                else $_SESSION['cart'][$id] = min($qty, (int)$stock);
                break;
            case 'remove':
                unset($_SESSION['cart'][$id]);
                break;
        }
    }
} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
}

header('Location: ' . $back);
exit;
