-- Tabela de Prestadores
CREATE TABLE IF NOT EXISTS `prestadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `sobrenome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Servicos
CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `descricao` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela Pivo: Prestadores x Servicos
CREATE TABLE IF NOT EXISTS `prestadores_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prestador_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prestador_id` (`prestador_id`),
  KEY `servico_id` (`servico_id`),
  UNIQUE KEY `prestador_servico_unico` (`prestador_id`, `servico_id`),
  CONSTRAINT `fk_prestador` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- TABELA DE AGENDAMENTOS (cabecalho)
-- ==========================================
CREATE TABLE IF NOT EXISTS `agendamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_nome` varchar(150) NOT NULL,
  `cliente_email` varchar(150) DEFAULT NULL,
  `cliente_telefone` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'rascunho',
  -- status sugeridos: rascunho, marcado, em_producao, concluido, cancelado
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observacoes` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- ITENS DO AGENDAMENTO (cada servico + prestador)
-- ==========================================
CREATE TABLE IF NOT EXISTS `agendamento_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agendamento_id` int(11) NOT NULL,
  `prestador_id` int(11) DEFAULT NULL,
  `servico_id` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `duracao_dias` int(11) NOT NULL DEFAULT 1,
  `exclusivo` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'rascunho',
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agendamento_id` (`agendamento_id`),
  KEY `prestador_id` (`prestador_id`),
  KEY `servico_id` (`servico_id`),
  CONSTRAINT `fk_item_agendamento` FOREIGN KEY (`agendamento_id`)
    REFERENCES `agendamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_prestador` FOREIGN KEY (`prestador_id`)
    REFERENCES `prestadores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_servico` FOREIGN KEY (`servico_id`)
    REFERENCES `servicos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Seed

-- Inserir serviços padrão
INSERT INTO `servicos` (`nome`, `descricao`, `created`, `modified`) VALUES
('Planejamento e Arquitetura', 'Projetos arquitetônicos e planejamento de obras', NOW(), NOW()),
('Diagnóstico e Consultoria Inicial', 'Análise e consultoria para início de projetos', NOW(), NOW()),
('Definição de Arquitetura e Stack Tecnológica', 'Definição de tecnologias e arquitetura de sistemas', NOW(), NOW()),
('Prototipação de Telas (Wireframes / UI Básica)', 'Criação de protótipos e wireframes', NOW(), NOW()),
('Design e Implementação do Frontend', 'Desenvolvimento de interfaces visuais', NOW(), NOW()),
('Desenvolvimento do Backend e APIs', 'Desenvolvimento de lógica de negócio e APIs', NOW(), NOW()),
('Modelagem e Configuração do Banco de Dados', 'Estruturação e otimização de bancos de dados', NOW(), NOW()),
('Sistema de Autenticação e Autorização', 'Implementação de login e controle de acesso', NOW(), NOW());

-- Inserir prestadores de exemplo (baseado no Figma)
INSERT INTO `prestadores` (`nome`, `sobrenome`, `email`, `telefone`, `created`, `modified`) VALUES
('Olivia', 'Rhye', 'olivia@untitledui.com', '(82) 99604-9202', NOW(), NOW()),
('Phoenix', 'Baker', 'phoenix@untitledui.com', '(82) 99604-9202', NOW(), NOW()),
('Lana', 'Steiner', 'lana@untitledui.com', '(82) 99504-9202', NOW(), NOW()),
('Demi', 'Wilkinson', 'demi@untitledui.com', '(82) 99604-9202', NOW(), NOW()),
('Candice', 'Wu', 'candice@untitledui.com', '(82) 99604-9202', NOW(), NOW()),
('Natali', 'Craig', 'natali@untitledui.com', '(82) 99604-9202', NOW(), NOW());

-- Relacionar prestadores com serviços e seus respectivos preços
INSERT INTO `prestadores_servicos` (`prestador_id`, `servico_id`, `valor`, `created`, `modified`) VALUES
-- Olivia Rhye - Planejamento e Arquitetura
(1, 1, 200.00, NOW(), NOW()),

-- Phoenix Baker - Diagnóstico e Consultoria Inicial
(2, 2, 200.00, NOW(), NOW()),

-- Lana Steiner - Definição de Arquitetura e Stack Tecnológica
(3, 3, 200.00, NOW(), NOW()),

-- Demi Wilkinson - Prototipação de Telas
(4, 4, 200.00, NOW(), NOW()),

-- Candice Wu - Design e Implementação do Frontend
(5, 5, 200.00, NOW(), NOW()),

-- Natali Craig - Desenvolvimento do Backend e APIs
(6, 6, 200.00, NOW(), NOW());