-- Usar o banco de dados correto
USE projeto_pim;

-- Criação da tabela 'usuarios'
CREATE TABLE IF NOT EXISTS usuarios (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` VARCHAR(110) COLLATE utf8mb4_unicode_ci NOT NULL,
    `telefone` VARCHAR(15) COLLATE utf8mb4_unicode_ci NOT NULL,
    `data_nasc` DATE NOT NULL,
    `senha` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tipo` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comum',
    `certificado` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, -- <<< ADICIONE ESTA LINHA (ou ajuste conforme necessário)
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email_UNIQUE` (`email`)
);

CREATE TABLE IF NOT EXISTS cursos (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT NULL,
  `categoria` VARCHAR(100) NULL,          -- Adicionando a coluna categoria que você usaDESCRIBE
  `total_aulas` INT NULL DEFAULT 0,        -- Adicionando a coluna total_aulas
  `professor_id` INT NULL,                 -- ID do usuário professor que criou/ministra o curso
  `data_criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_cursos_professor_idx` (`professor_id` ASC), -- Opcional, mas bom para performance
  CONSTRAINT `fk_cursos_professor`
    FOREIGN KEY (`professor_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE SET NULL -- Se o professor for deletado, o curso fica sem professor, mas não é deletado
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aluno_cursos (
  `aluno_id` INT NOT NULL,
  `curso_id` INT NOT NULL,
  `data_inscricao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `progresso` INT NULL DEFAULT 0,                     -- Ex: progresso de 0 a 100
  `aulas_concluidas` INT NULL DEFAULT 0,            -- Adicionando aulas_concluidas
  PRIMARY KEY (`aluno_id`, `curso_id`),
  INDEX `fk_aluno_cursos_curso_idx` (`curso_id` ASC),
  INDEX `fk_aluno_cursos_aluno_idx` (`aluno_id` ASC),
  CONSTRAINT `fk_aluno_cursos_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE -- Se o aluno for deletado, suas inscrições são removidas
    ON UPDATE CASCADE,
  CONSTRAINT `fk_aluno_cursos_curso`
    FOREIGN KEY (`curso_id`)
    REFERENCES `cursos` (`id`)
    ON DELETE CASCADE -- Se o curso for deletado, as inscrições nele são removidas
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(255) NOT NULL,        -- Corresponde ao 'titulo' do evento no FullCalendar
  `data_evento` DATETIME NOT NULL,     -- Corresponde ao 'start' (e 'end' se necessário)
  `descricao` TEXT NULL,               -- Detalhes do evento para o modal
  `curso_associado` VARCHAR(255) NULL, -- Nome do curso ou referência para o modal
  `aluno_id` INT NULL,                 -- Se o evento for específico para um aluno
  `cor` VARCHAR(7) NULL,               -- Para a cor do evento no calendário (ex: '#FFD700')
  FOREIGN KEY (`aluno_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
  -- Você pode adicionar uma FK para cursos.id se 'curso_associado' for um ID de curso
  -- FOREIGN KEY (`curso_relacionado_id`) REFERENCES `cursos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (Opcional) Inserir alguns dados iniciais para teste, se desejar
-- Exemplo:
-- INSERT INTO usuarios (nome, email, telefone, data_nasc, senha, tipo, certificado) VALUES ('Admin User', 'admin@exemplo.com', '11912345678', '1980-01-01', 'hash_da_senha_admin', 'administrador', '/caminho/do/certificado_admin.pdf');