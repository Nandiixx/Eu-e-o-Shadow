<?php
require_once __DIR__ . '/../includes/db_include.php';

class ClienteModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Create
    public function criar($dados) {
        try {
            $this->pdo->beginTransaction();

            // Primeiro insere o usuário
            $sqlUsuario = "INSERT INTO Usuario (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)";
            $stmtUsuario = $this->pdo->prepare($sqlUsuario);
            $stmtUsuario->execute([
                ':nome' => $dados['nome'],
                ':email' => $dados['email'],
                ':senha_hash' => password_hash($dados['senha'] ?? 'mudar123', PASSWORD_DEFAULT) // senha padrão temporária
            ]);
            
            $usuarioId = $this->pdo->lastInsertId();

            // Depois insere o cliente
            $sqlCliente = "INSERT INTO Cliente (usuario_id, telefone) VALUES (:usuario_id, :telefone)";
            $stmtCliente = $this->pdo->prepare($sqlCliente);
            $stmtCliente->execute([
                ':usuario_id' => $usuarioId,
                ':telefone' => $dados['telefone']
            ]);

            $this->pdo->commit();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'email') !== false) {
                throw new Exception("Este email já está cadastrado");
            }
            error_log("Erro ao criar cliente: " . $e->getMessage());
            throw new Exception("Não foi possível criar o cliente");
        }
    }

    // Read
    public function buscarPorId($id) {
        $sql = "SELECT c.id, u.nome, u.email, c.telefone 
                FROM Cliente c 
                JOIN Usuario u ON c.usuario_id = u.id 
                WHERE c.id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar cliente: " . $e->getMessage());
            throw new Exception("Cliente não encontrado");
        }
    }

    public function buscarPorEmail($email) {
        $sql = "SELECT c.id, u.nome, u.email, c.telefone 
                FROM Cliente c 
                JOIN Usuario u ON c.usuario_id = u.id 
                WHERE u.email = :email";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar cliente por email: " . $e->getMessage());
            throw new Exception("Erro ao buscar cliente");
        }
    }

    public function listarTodos($filtros = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filtros['nome'])) {
            $where .= " AND u.nome LIKE :nome";
            $params[':nome'] = "%{$filtros['nome']}%";
        }

        if (!empty($filtros['email'])) {
            $where .= " AND u.email LIKE :email";
            $params[':email'] = "%{$filtros['email']}%";
        }

        if (!empty($filtros['telefone'])) {
            $where .= " AND c.telefone LIKE :telefone";
            $params[':telefone'] = "%{$filtros['telefone']}%";
        }

        $sql = "SELECT c.id, u.nome, u.email, c.telefone 
                FROM Cliente c 
                JOIN Usuario u ON c.usuario_id = u.id 
                WHERE {$where} 
                ORDER BY u.nome";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar clientes: " . $e->getMessage());
            throw new Exception("Não foi possível listar os clientes");
        }
    }

    // Update
    public function atualizar($id, $dados) {
        try {
            $this->pdo->beginTransaction();

            // Primeiro busca o usuário_id do cliente
            $sqlBusca = "SELECT usuario_id FROM Cliente WHERE id = :id";
            $stmtBusca = $this->pdo->prepare($sqlBusca);
            $stmtBusca->execute([':id' => $id]);
            $usuarioId = $stmtBusca->fetchColumn();

            if (!$usuarioId) {
                throw new Exception("Cliente não encontrado");
            }

            // Atualiza dados do usuário
            if (isset($dados['nome']) || isset($dados['email'])) {
                $camposUsuario = [];
                $paramsUsuario = [':usuario_id' => $usuarioId];

                if (isset($dados['nome'])) {
                    $camposUsuario[] = "nome = :nome";
                    $paramsUsuario[':nome'] = $dados['nome'];
                }
                if (isset($dados['email'])) {
                    $camposUsuario[] = "email = :email";
                    $paramsUsuario[':email'] = $dados['email'];
                }

                if (!empty($camposUsuario)) {
                    $sqlUsuario = "UPDATE Usuario SET " . implode(', ', $camposUsuario) . " WHERE id = :usuario_id";
                    $stmtUsuario = $this->pdo->prepare($sqlUsuario);
                    $stmtUsuario->execute($paramsUsuario);
                }
            }

            // Atualiza dados do cliente
            if (isset($dados['telefone'])) {
                $sqlCliente = "UPDATE Cliente SET telefone = :telefone WHERE id = :id";
                $stmtCliente = $this->pdo->prepare($sqlCliente);
                $stmtCliente->execute([
                    ':id' => $id,
                    ':telefone' => $dados['telefone']
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'email') !== false) {
                throw new Exception("Este email já está cadastrado");
            }
            error_log("Erro ao atualizar cliente: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar o cliente");
        }
    }

    // Delete
    public function excluir($id) {
        try {
            // Verifica se existem agendamentos para este cliente
            $sql = "SELECT COUNT(*) FROM Agendamento WHERE cliente_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Não é possível excluir este cliente pois existem agendamentos vinculados");
            }

            $this->pdo->beginTransaction();

            // Primeiro busca o usuário_id
            $sqlBusca = "SELECT usuario_id FROM Cliente WHERE id = :id";
            $stmtBusca = $this->pdo->prepare($sqlBusca);
            $stmtBusca->execute([':id' => $id]);
            $usuarioId = $stmtBusca->fetchColumn();

            if (!$usuarioId) {
                throw new Exception("Cliente não encontrado");
            }

            // Exclui o cliente primeiro (devido à chave estrangeira)
            $sqlCliente = "DELETE FROM Cliente WHERE id = :id";
            $stmtCliente = $this->pdo->prepare($sqlCliente);
            $stmtCliente->execute([':id' => $id]);

            // Depois exclui o usuário
            $sqlUsuario = "DELETE FROM Usuario WHERE id = :id";
            $stmtUsuario = $this->pdo->prepare($sqlUsuario);
            $stmtUsuario->execute([':id' => $usuarioId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao excluir cliente: " . $e->getMessage());
            throw new Exception("Não foi possível excluir o cliente");
        }
    }
}