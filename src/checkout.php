<?php
require __DIR__ . '/config.php';
require __DIR__ . '/layout.php';

$items   = cart_items();
$error   = null;
$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $items) {
    csrf_check();
    $pdo = db();
    try {
        $pdo->beginTransaction();
        $total = 0.0;

        // trava as linhas dos produtos para evitar venda acima do estoque
        $lock = $pdo->prepare('SELECT id, name, price, stock FROM products WHERE id = ? FOR UPDATE');
        $rows = [];
        foreach ($items as $it) {
            $lock->execute([$it['id']]);
            $p = $lock->fetch();
            if (!$p || $p['stock'] < $it['qty']) {
                throw new RuntimeException('Estoque insuficiente para: ' . ($p['name'] ?? 'produto'));
            }
            $rows[] = [$p, $it['qty']];
            $total += $it['qty'] * (float)$p['price'];
        }

        $st = $pdo->prepare('INSERT INTO orders (total) VALUES (?) RETURNING id');
        $st->execute([$total]);
        $orderId = (int)$st->fetchColumn();

        $ins = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?,?,?,?)');
        $upd = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
        foreach ($rows as [$p, $qty]) {
            $ins->execute([$orderId, $p['id'], $qty, $p['price']]);
            $upd->execute([$qty, $p['id']]);
        }

        $pdo->commit();
        $_SESSION['cart'] = [];
        $items = [];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = $e instanceof RuntimeException ? $e->getMessage() : 'Erro ao finalizar o pedido.';
    }
}

render_header('Fechar pedido');
?>
<h1>Fechar pedido</h1>

<?php if ($orderId): ?>
  <div class="panel success">
    <h2>✅ Pedido #<?= $orderId ?> realizado com sucesso!</h2>
    <p>Obrigado pela compra. (Pedido salvo no PostgreSQL e estoque atualizado.)</p>
    <a class="btn inline" href="index.php">Voltar à loja</a>
  </div>
<?php elseif (!$items): ?>
  <div class="panel"><p class="empty">Seu carrinho está vazio.</p>
  <a class="btn inline" href="index.php">Ver produtos</a></div>
<?php else: ?>
  <?php if ($error): ?><div class="panel error"><?= h($error) ?></div><?php endif; ?>
  <div class="panel">
    <?php foreach ($items as $it): ?>
      <div class="cart-row">
        <div class="thumb small"><?= h($it['emoji']) ?></div>
        <div class="info"><h3><?= h($it['name']) ?></h3><p>Qtd: <?= (int)$it['qty'] ?></p></div>
        <div class="price"><?= money($it['subtotal']) ?></div>
      </div>
    <?php endforeach; ?>
    <p class="subtotal-line">Total do pedido: <strong><?= money(cart_total($items)) ?></strong></p>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <button class="btn inline" type="submit">Finalizar compra</button>
    </form>
  </div>
<?php endif; ?>
<?php render_footer(); ?>
