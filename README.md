# 🛒 Amazon Cart — Carrinho de compras em PHP + PostgreSQL

Clone simplificado do fluxo de compra da Amazon: catálogo, busca, filtro por categoria,
carrinho e finalização de pedido com controle de estoque.

## Funcionalidades
- Catálogo de produtos vindo do PostgreSQL (busca `ILIKE` + filtro por categoria)
- Carrinho em sessão: adicionar, alterar quantidade, remover, esvaziar
- Checkout com **transação** (`BEGIN/COMMIT`) e `SELECT ... FOR UPDATE` para evitar overselling
- Pedidos gravados em `orders` / `order_items`, estoque atualizado
- Segurança: PDO com prepared statements, proteção CSRF, escape de saída (XSS), redirect seguro
- Layout responsivo em CSS puro

## Stack
PHP 8.3 · PostgreSQL 16 · HTML5 · CSS3 · Docker

## Como rodar com Docker
```bash
docker compose up --build
# acesse http://localhost:8080
```

## Como rodar sem Docker
```bash
createdb -U postgres amazon_cart
psql -U postgres -d amazon_cart -f db/schema.sql
# precisa da extensão pdo_pgsql habilitada
DB_USER=postgres DB_PASS=suasenha php -S localhost:8000 -t src
```

Variáveis aceitas: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` ou `DATABASE_URL`.

## Estrutura
```
src/   index.php, cart.php, checkout.php, actions.php, config.php, layout.php, style.css
db/    schema.sql (tabelas + produtos de exemplo)
```

## Modelo de dados
`products` 1—N `order_items` N—1 `orders`
