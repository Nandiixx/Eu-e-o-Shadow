-- Tabela base para todos os usuários que podem fazer login
CREATE TABLE Usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL -- Armazenar senhas como hash
);

-- Tabela para Clientes, ligada a um Usuário
-- O cliente pode se cadastrar  e fazer login
CREATE TABLE Cliente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL UNIQUE,
    telefone VARCHAR(20),
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id)
);

-- Tabela para Funcionários, também ligados a um Usuário
-- Abrange Recepcionista, Profissional, Proprietário e Gerente Financeiro
CREATE TABLE Funcionario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL UNIQUE,
    matricula VARCHAR(50) NOT NULL UNIQUE,
    cargo ENUM('RECEPCIONISTA', 'PROFISSIONAL_BELEZA', 'PROPRIETARIO', 'GERENTE_FINANCEIRO') NOT NULL,
    especialidade VARCHAR(100), -- Aplicável apenas se cargo = 'PROFISSIONAL_BELEZA'
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id)
);

-- Tabela para os Serviços oferecidos
CREATE TABLE Servico (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    duracao_minutos INT NOT NULL -- Duração estimada para ajudar na agenda
);

-- Tabela central de Agendamentos
-- Relaciona um Cliente a um Profissional em uma data/hora específica
CREATE TABLE Agendamento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('AGENDADO', 'CONCLUIDO', 'CANCELADO') NOT NULL DEFAULT 'AGENDADO',
    
    FOREIGN KEY (cliente_id) REFERENCES Cliente(id),
    -- Garante que o funcionário agendado é um profissional de beleza
    FOREIGN KEY (profissional_id) REFERENCES Funcionario(id) 
    -- Adicionar um CHECK constraint aqui seria ideal para garantir cargo = 'PROFISSIONAL_BELEZA',
    -- mas a sintaxe pode variar entre SGBDs (ex: MySQL não suporta CHECK em FKs diretamente)
);

-- Tabela de ligação (muitos-para-muitos)
-- Permite que um agendamento tenha um ou mais serviços
CREATE TABLE Agendamento_Servicos (
    agendamento_id INT NOT NULL,
    servico_id INT NOT NULL,
    PRIMARY KEY (agendamento_id, servico_id), -- Chave primária composta
    FOREIGN KEY (agendamento_id) REFERENCES Agendamento(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES Servico(id)
);