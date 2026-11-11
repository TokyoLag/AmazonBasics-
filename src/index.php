<?php
require __DIR__ . '/config.php';
require __DIR__ . '/layout.php';

$q   = trim($_GET['q'] ?? '');
$cat = trim($_GET['cat'] ?? '');

$sql = 'SELECT * FROM products WHERE 1=1';
$params = [];
if ($q !== '')   { $sql .= ' AND name ILIKE :q';      $params[':q']   = "%$q%"; }
if ($cat !== '') { $sql .= ' AND category = :cat';    $params[':cat'] = $cat; }
$sql .= ' ORDER BY id';

$st = db()->prepare($sql);
$st->execute($params);
$products = $st->fetchAll();
$categories = db()->query('SELECT DISTINCT category FROM products ORDER BY 1')->fetchAll(PDO::FETCH_COLUMN);

render_header('Amazon Cart · Produtos');
?>
<nav class="categories">
  <a href="index.php" class="<?= $cat === '' ? 'active' : '' ?>">Todos</a>
  <?php foreach ($categories as $c): ?>
    <a href="index.php?cat=<?= urlencode($c) ?>" class="<?= $cat === $c ? 'active' : '' ?>"><?= h($c) ?></a>
  <?php endforeach; ?>
</nav>

<?php if (!$products): ?>
  <p class="empty">Nenhum produto encontrado.</p>
<?php endif; ?>

<section class="grid">
<?php foreach ($products as $p): ?>
  <article class="card">
    <div class="thumb"><?= h($p['emoji']) ?></div>
    <h3><?= h($p['name']) ?></h3>
    <p class="desc"><?= h($p['description']) ?></p>
    <p class="price"><?= money($p['price']) ?></p>
    <?php if ($p['stock'] > 0): ?>
      <p class="stock ok">Em estoque (<?= (int)$p['stock'] ?>)</p>
      <form action="actions.php" method="post">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <input type="hidden" name="back" value="<?= h($_SERVER['REQUEST_URI']) ?>">
        <button class="btn" type="submit">Adicionar ao carrinho</button>
      </form>
    <?php else: ?>
      <p class="stock out">Indisponível</p>
    <?php endif; ?>
  </article>
<?php endforeach; ?>
</section>
<?php render_footer(); ?>
