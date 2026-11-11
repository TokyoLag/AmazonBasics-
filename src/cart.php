<?php
require __DIR__ . '/config.php';
require __DIR__ . '/layout.php';

$items = cart_items();
$total = cart_total($items);

render_header('Carrinho de compras');
?>
<h1>Carrinho de compras</h1>

<?php if (!$items): ?>
  <div class="panel"><p class="empty">Seu carrinho está vazio.</p>
  <a class="btn inline" href="index.php">Continuar comprando</a></div>
<?php else: ?>
<div class="cart-layout">
  <section class="panel">
    <?php foreach ($items as $it): ?>
      <div class="cart-row">
        <div class="thumb small"><?= h($it['emoji']) ?></div>
        <div class="info">
          <h3><?= h($it['name']) ?></h3>
          <p class="stock ok">Em estoque</p>
          <div class="row-actions">
            <form action="actions.php" method="post" class="qty-form">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <label>Qtd:
                <input type="number" name="qty" min="0" max="<?= (int)$it['stock'] ?>" value="<?= (int)$it['qty'] ?>">
              </label>
              <button class="link" type="submit">Atualizar</button>
            </form>
            <form action="actions.php" method="post">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button class="link" type="submit">Excluir</button>
            </form>
          </div>
        </div>
        <div class="price"><?= money($it['subtotal']) ?></div>
      </div>
    <?php endforeach; ?>
    <p class="subtotal-line">Subtotal (<?= cart_count() ?> itens): <strong><?= money($total) ?></strong></p>
  </section>

  <aside class="panel summary">
    <p>Subtotal (<?= cart_count() ?> itens): <strong><?= money($total) ?></strong></p>
    <a class="btn inline" href="checkout.php">Fechar pedido</a>
    <form action="actions.php" method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="clear">
      <button class="link" type="submit">Esvaziar carrinho</button>
    </form>
  </aside>
</div>
<?php endif; ?>
<?php render_footer(); ?>
