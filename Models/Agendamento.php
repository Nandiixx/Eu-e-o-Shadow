<?php
require_once __DIR__ . '/../includes/db_include.php';

class AgendamentoModel {
    private $table = 'agendamentos';
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Create
    public function criar($dados) {
        $sql = "INSERT INTO {$this->table} (cliente_id, profissional_id, servico_id, data_hora, status) 
                VALUES (:cliente_id, :profissional_id, :servico_id, :data_hora, :status)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':cliente_id' => $dados['cliente_id'],
                ':profissional_id' => $dados['profissional_id'],
                ':servico_id' => $dados['servico_id'],
                ':data_hora' => $dados['data_hora'],
                ':status' => $dados['status'] ?? 'pendente'
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar agendamento: " . $e->getMessage());
            throw new Exception("Não foi possível criar o agendamento");
        }
    }

    // Read
    public function buscarPorId($id) {
        $sql = "SELECT a.*, c.nome as cliente_nome, p.nome as profissional_nome, s.nome as servico_nome 
                FROM {$this->table} a 
                JOIN clientes c ON a.cliente_id = c.id
                JOIN profissionais p ON a.profissional_id = p.id
                JOIN servicos s ON a.servico_id = s.id
                WHERE a.id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendamento: " . $e->getMessage());
            throw new Exception("Agendamento não encontrado");
        }
    }

    public function listarTodos($filtros = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filtros['cliente_id'])) {
            $where .= " AND a.cliente_id = :cliente_id";
            $params[':cliente_id'] = $filtros['cliente_id'];
        }

        if (!empty($filtros['profissional_id'])) {
            $where .= " AND a.profissional_id = :profissional_id";
            $params[':profissional_id'] = $filtros['profissional_id'];
        }

        if (!empty($filtros['data'])) {
            $where .= " AND DATE(a.data_hora) = :data";
            $params[':data'] = $filtros['data'];
        }

        $sql = "SELECT a.*, c.nome as cliente_nome, p.nome as profissional_nome, s.nome as servico_nome 
                FROM {$this->table} a 
                JOIN clientes c ON a.cliente_id = c.id
                JOIN profissionais p ON a.profissional_id = p.id
                JOIN servicos s ON a.servico_id = s.id
                WHERE {$where}
                ORDER BY a.data_hora";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar agendamentos: " . $e->getMessage());
            throw new Exception("Não foi possível listar os agendamentos");
        }
    }

    // Update
    public function atualizar($id, $dados) {
        $campos = [];
        $params = [':id' => $id];

        foreach ($dados as $campo => $valor) {
            if (in_array($campo, ['cliente_id', 'profissional_id', 'servico_id', 'data_hora', 'status'])) {
                $campos[] = "{$campo} = :{$campo}";
                $params[":{$campo}"] = $valor;
            }
        }

        if (empty($campos)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $campos) . " WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar agendamento: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar o agendamento");
        }
    }

    // Delete
    public function excluir($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erro ao excluir agendamento: " . $e->getMessage());
            throw new Exception("Não foi possível excluir o agendamento");
        }
    }

    // Métodos auxiliares
    public function verificarDisponibilidade($profissional_id, $data_hora) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE profissional_id = :profissional_id 
                AND data_hora = :data_hora
                AND status != 'cancelado'";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':profissional_id' => $profissional_id,
                ':data_hora' => $data_hora
            ]);
            return $stmt->fetchColumn() == 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar disponibilidade: " . $e->getMessage());
            throw new Exception("Não foi possível verificar a disponibilidade");
        }
    }
}
