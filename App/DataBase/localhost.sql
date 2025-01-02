-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 02/01/2025 às 09:15
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `frang566_frangonacaixa`
--
CREATE DATABASE IF NOT EXISTS `frang566_frangonacaixa` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `frang566_frangonacaixa`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `imagem`) VALUES
(4, 'Molhoss', 'https://via.placeholder.com/200?text=Molhos'),
(6, 'Anéis de Cebolas', 'https://via.placeholder.com/200?text=Anéis+de+Cebola'),
(7, 'Hambúrgueres de Carne', 'https://via.placeholder.com/200?text=Hambúrgueres+de+Carnes'),
(12, 'Refrigerante', NULL),
(13, 'Sucos', NULL),
(14, 'Lanches', NULL),
(15, 'Sobremesas', NULL),
(16, 'Molhossn', NULL),
(17, 'Petiscos', NULL),
(18, 'Pizzas', NULL),
(19, 'Massas', NULL),
(20, 'Bebidas Alcoólicas', NULL),
(21, 'Café e Chá', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Ingredientes`
--

CREATE TABLE `Ingredientes` (
  `Id` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `ItemId` int(11) NOT NULL,
  `Quantidade` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `Ingredientes`
--

INSERT INTO `Ingredientes` (`Id`, `Nome`, `ItemId`, `Quantidade`) VALUES
(1, 'Molho de Tomate', 81, 1),
(2, 'Maionese', 81, 1),
(3, 'Farinha de Trigo', 82, 2),
(4, 'Ovo', 82, 1),
(5, 'Cebola', 82, 3),
(6, 'Pão de Hambúrguer', 83, 1),
(7, 'Carne de Hambúrguer', 83, 1),
(8, 'Queijo', 83, 1),
(9, 'Tomate', 83, 1),
(10, 'Alface', 83, 1),
(11, 'Açúcar', 87, 1),
(12, 'Leite Condensado', 87, 1),
(13, 'Chocolate', 87, 1),
(14, 'Massa de Pizza', 90, 1),
(15, 'Queijo', 90, 2),
(16, 'Presunto', 90, 2),
(17, 'Molho de Tomate', 90, 1),
(18, 'Macarrão', 91, 1),
(19, 'Molho de Tomate', 91, 1),
(20, 'Queijo', 91, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens`
--

CREATE TABLE `itens` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `itens`
--

INSERT INTO `itens` (`id`, `nome`, `preco`, `imagem`, `categoria_id`) VALUES
(81, 'ketchup', 8.00, 'https://via.placeholder.com/200?text=Molhos', 4),
(82, 'Anéis de Cebola', 12.00, 'https://via.placeholder.com/200?text=Anéis+de+Cebola', 6),
(83, 'x-tudo', 18.00, 'https://via.placeholder.com/200?text=Hamburgueres+de+Carne', 7),
(84, 'Coca-cola', 5.00, NULL, 12),
(85, 'Tang', 4.00, NULL, 13),
(86, 'x-bocão', 15.00, NULL, 14),
(87, 'picolé', 10.00, NULL, 15),
(88, 'mostarda', 8.00, NULL, 16),
(89, 'batata-frita', 10.00, NULL, 17),
(90, 'Catupiry', 30.00, NULL, 18),
(91, 'Miojo', 25.00, NULL, 19),
(92, 'Brahma', 7.00, NULL, 20),
(93, 'cafezin', 5.00, NULL, 21);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `json_usuario` json NOT NULL,
  `json_pedido` json NOT NULL,
  `status_pagamento` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'pendente',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metodo_pagamento` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `json_usuario`, `json_pedido`, `status_pagamento`, `data_criacao`, `metodo_pagamento`, `total`, `observacoes`) VALUES
(113, '{\"name\": \"Luiz Rodrigues Castro Neto\", \"email\": \"luizrodriguescastroneto@gmail.com\", \"total\": \"R$ 32.00\", \"number\": \"\", \"address\": \"\", \"reference\": \"\", \"complement\": \"\"}', '[{\"id\": 88, \"nome\": \"mostarda\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": []}, {\"id\": 88, \"nome\": \"mostarda\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": []}, {\"id\": 81, \"nome\": \"ketchup\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": [{\"nome\": \"Molho de Tomate\", \"quantidade\": 1}, {\"nome\": \"Maionese\", \"quantidade\": 1}]}, {\"id\": 88, \"nome\": \"mostarda\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": []}]', 'despachado', '2025-01-01 05:28:46', NULL, '32.00', NULL),
(114, '{\"name\": \"Luiz Rodrigues Castro Neto\", \"email\": \"luizrodriguescastroneto@gmail.com\", \"total\": \"R$ 32.00\", \"number\": \"\", \"address\": \"\", \"reference\": \"\", \"complement\": \"\"}', '[{\"id\": 88, \"nome\": \"mostarda\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": []}, {\"id\": 88, \"nome\": \"mostarda\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": []}, {\"id\": 81, \"nome\": \"ketchup\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": [{\"nome\": \"Molho de Tomate\", \"quantidade\": 1}, {\"nome\": \"Maionese\", \"quantidade\": 1}]}, {\"id\": 88, \"nome\": \"mostarda\", \"preco\": \"8.00\", \"quantidade\": 1, \"ingredientes\": []}]', 'pendente', '2025-01-01 23:07:20', NULL, '32.00', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_criacao`) VALUES
(2, 'neto', 'neto@neto.com', '123', '2024-12-24 22:37:40');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `Ingredientes`
--
ALTER TABLE `Ingredientes`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itens_ibfk_1` (`categoria_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `Ingredientes`
--
ALTER TABLE `Ingredientes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `itens`
--
ALTER TABLE `itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `itens_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
