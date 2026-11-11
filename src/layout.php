<?php
function render_header(string $title = 'Amazon Cart'): void
{
    $q = h($_GET['q'] ?? '');
    $n = cart_count();
    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$title}</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <a class="logo" href="index.php">amazon<span>.clone</span></a>
  <form class="search" action="index.php" method="get">
    <input type="text" name="q" value="{$q}" placeholder="Buscar produtos">
    <button type="submit">🔍</button>
  </form>
  <a class="cart-link" href="cart.php">🛒 <strong>{$n}</strong> Carrinho</a>
</header>
<main class="container">
HTML;
}

function render_footer(): void
{
    echo '</main><footer class="footer">Projeto de portfólio · PHP + PostgreSQL · Não é afiliado à Amazon</footer></body></html>';
}
