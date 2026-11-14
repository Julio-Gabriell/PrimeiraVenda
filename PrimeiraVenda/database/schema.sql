PRAGMA foreign_keys = ON;

CREATE TABLE produto (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    descricao TEXT,
    categoria TEXT,
    preco REAL NOT NULL CHECK (preco >= 0),
    imagem_url TEXT,
    ativo INTEGER NOT NULL DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE estoque (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    produto_id INTEGER NOT NULL UNIQUE,
    quantidade INTEGER NOT NULL DEFAULT 0 CHECK (quantidade >= 0),
    ultima_origem TEXT DEFAULT 'CADASTRO_WHATSAPP', -- 'CADASTRO_WHATSAPP', 'VENDA_ONLINE', 'BAIXA_BALCAO'
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE CASCADE
);

CREATE TABLE pedido (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cliente_nome TEXT NOT NULL,
    cliente_telefone TEXT,
    valor_total REAL NOT NULL CHECK (valor_total >= 0),
    status TEXT NOT NULL DEFAULT 'PENDENTE', -- 'PENDENTE', 'PAGO', 'CANCELADO'
    pix_copia_cola TEXT,
    checkout_url TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    pago_em DATETIME
);

CREATE TABLE item_pedido (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pedido_id INTEGER NOT NULL,
    produto_id INTEGER NOT NULL,
    quantidade INTEGER NOT NULL DEFAULT 1 CHECK (quantidade > 0),
    preco_unitario REAL NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produto(id)
);

INSERT INTO produto (nome, descricao, categoria, preco, imagem_url) VALUES
 ('Camiseta Básica Preta G', 'Camiseta 100% algodão, cor preta, tamanho G.', 'Roupas', 49.90, NULL),
 ('Tênis Casual Branco Tam 40', 'Tênis casual branco, tamanho 40.', 'Calçados', 189.90, NULL),
 ('Relógio Couro Marrom', 'Relógio analógico com pulseira de couro marrom.', 'Acessórios', 129.90, NULL);

INSERT INTO estoque (produto_id, quantidade, ultima_origem) VALUES
 (1, 5, 'CADASTRO_WHATSAPP'),
 (2, 3, 'CADASTRO_WHATSAPP'),
 (3, 2, 'CADASTRO_WHATSAPP');

INSERT INTO pedido (cliente_nome, cliente_telefone, valor_total, status, pix_copia_cola, checkout_url) VALUES
 ('Maria Silva', '5511999999999', 49.90, 'PENDENTE',
  '00020126580014br.gov.bcb.pix0136exemplo-chave-pix-0000-0000-00005204000053039865406049.905802BR5909PrimeiraVenda6009SAO PAULO62070503***6304ABCD',
  'https://exemplo.com/checkout/1');

INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES
 (1, 1, 1, 49.90);
