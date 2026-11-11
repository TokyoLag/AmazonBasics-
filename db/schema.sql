CREATE TABLE IF NOT EXISTS products (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    price       NUMERIC(10,2) NOT NULL CHECK (price >= 0),
    category    VARCHAR(60) NOT NULL,
    emoji       VARCHAR(8) NOT NULL DEFAULT '📦',
    stock       INTEGER NOT NULL DEFAULT 0 CHECK (stock >= 0)
);

CREATE TABLE IF NOT EXISTS orders (
    id         SERIAL PRIMARY KEY,
    total      NUMERIC(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS order_items (
    id         SERIAL PRIMARY KEY,
    order_id   INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id),
    quantity   INTEGER NOT NULL CHECK (quantity > 0),
    unit_price NUMERIC(10,2) NOT NULL
);

INSERT INTO products (name, description, price, category, emoji, stock) VALUES
('Fone de Ouvido Bluetooth', 'Cancelamento de ruído, 30h de bateria.', 249.90, 'Eletrônicos', '🎧', 25),
('Smartwatch Fit', 'Monitor cardíaco, GPS e resistência à água.', 399.00, 'Eletrônicos', '⌚', 15),
('Teclado Mecânico', 'Switch azul, retroiluminado RGB.', 289.50, 'Eletrônicos', '⌨️', 30),
('Mouse Gamer', '12.000 DPI, 6 botões programáveis.', 129.90, 'Eletrônicos', '🖱️', 40),
('Livro: Clean Code', 'Habilidades práticas do Agile Software.', 89.90, 'Livros', '📘', 50),
('Livro: O Programador Pragmático', 'Da jornada ao mestre.', 79.90, 'Livros', '📗', 35),
('Cafeteira Elétrica', 'Capacidade de 1,2L, filtro permanente.', 159.00, 'Casa', '☕', 20),
('Luminária de Mesa LED', 'Três níveis de brilho, USB.', 69.90, 'Casa', '💡', 60),
('Mochila Executiva', 'Compartimento para notebook até 15,6".', 179.90, 'Moda', '🎒', 22),
('Tênis de Corrida', 'Amortecimento responsivo e leve.', 319.90, 'Moda', '👟', 18),
('Garrafa Térmica 1L', 'Mantém gelado por 24h.', 59.90, 'Casa', '🧴', 80),
('Cadeira Gamer', 'Ergonômica, reclinável 180°.', 899.00, 'Casa', '🪑', 8);
