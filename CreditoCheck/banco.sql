-- ============================================
-- Sistema de Analise de Credito (SPC/Serasa)
-- Execute este script no HeidiSQL
-- ============================================

CREATE DATABASE IF NOT EXISTS sistema_credito;
USE sistema_credito;

-- Tabela de clientes
-- Guarda os dados basicos do cliente e sua situacao de credito.
CREATE TABLE IF NOT EXISTS clientes (
    -- AUTO_INCREMENT faz o MySQL gerar um id novo automaticamente.
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    -- UNIQUE impede cadastrar dois clientes com o mesmo CPF.
    cpf VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(100),
    telefone VARCHAR(15),
    -- ENUM limita o campo a apenas estes dois valores.
    status_credito ENUM('Perfil confiavel', 'Pendencia') NOT NULL DEFAULT 'Perfil confiavel',
    -- CURRENT_TIMESTAMP grava automaticamente a data/hora do cadastro.
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de solicitacoes de emprestimo
-- Cada registro representa uma tentativa de emprestimo feita por um cliente.
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- cliente_id aponta para o id da tabela clientes.
    cliente_id INT NOT NULL,
    valor_solicitado DECIMAL(10,2) NOT NULL,
    status_solicitacao ENUM('Aprovada', 'Negada') NOT NULL,
    data_solicitacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- A chave estrangeira garante que a solicitacao pertence a um cliente existente.
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Dados de exemplo
-- Estes INSERTs ajudam a testar o sistema logo depois de criar o banco.
INSERT INTO clientes (nome, cpf, email, telefone, status_credito) VALUES
('João da Silva',    '111.222.333-44', 'joao@email.com',   '(11) 99999-0001', 'Perfil confiavel'),
('Maria Oliveira',  '222.333.444-55', 'maria@email.com',  '(11) 99999-0002', 'Pendencia'),
('Carlos Souza',    '333.444.555-66', 'carlos@email.com', '(11) 99999-0003', 'Perfil confiavel'),
('Ana Ferreira',    '444.555.666-77', 'ana@email.com',    '(11) 99999-0004', 'Pendencia'),
('Pedro Costa',     '555.666.777-88', 'pedro@email.com',  '(11) 99999-0005', 'Perfil confiavel');
