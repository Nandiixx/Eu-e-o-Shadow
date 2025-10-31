<?php
class Agendamento
{
    private $id;
    private $cliente_id;
    private $profissional_id;
    private $data_hora;
    private $status;
    private $servicos = []; // Array de IDs de serviços

    // --- Getters e Setters ---
    public function setClienteId($id) { $this->cliente_id = $id; }
    public function setProfissionalId($id) { $this->profissional_id = $id; }
    public function setDataHora($dt) { $this->data_hora = $dt; }
    public function setStatus($st) { $this->status = $st; }
    public function addServico($servico_id) { $this->servicos[] = $servico_id; }


    // --- Métodos de Banco de Dados ---
    
    public function inserirBD()
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();
            
            // Inicia transação
            $pdo->beginTransaction();

            // 1. Insere na tabela Agendamento
            $sql = "INSERT INTO Agendamento (cliente_id, profissional_id, data_hora, status) 
                    VALUES (:cliente_id, :profissional_id, :data_hora, :status)";
            
            $stmt = $pdo->prepare($sql);
            $status = 'AGENDADO'; // Default
            $stmt->execute([
                ':cliente_id' => $this->cliente_id,
                ':profissional_id' => $this->profissional_id,
                ':data_hora' => $this->data_hora,
                ':status' => $status
            ]);
            
            $agendamento_id = $pdo->lastInsertId();

            // 2. Insere na tabela Agendamento_Servicos
            $sql_serv = "INSERT INTO Agendamento_Servicos (agendamento_id, servico_id) VALUES (:agendamento_id, :servico_id)";
            $stmt_serv = $pdo->prepare($sql_serv);

            foreach ($this->servicos as $servico_id) {
                $stmt_serv->execute([
                    ':agendamento_id' => $agendamento_id,
                    ':servico_id' => $servico_id
                ]);
            }
            
            // Se tudo deu certo, comita
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            // Se algo deu errado, faz rollback
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log("Error in inserirBD: " . $e->getMessage());
            return false;
        }
    }
    
    public function listarAgendamentosCliente($cliente_id)
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();
            $lista = [];

            // Query complexa para buscar nomes em vez de IDs
            $sql = "SELECT 
                        a.id,
                        c_user.nome AS cliente_nome,
                        f_user.nome AS profissional_nome,
                        GROUP_CONCAT(s.nome SEPARATOR ', ') AS servicos,
                        a.data_hora,
                        a.status
                    FROM Agendamento a
                    JOIN Cliente c ON a.cliente_id = c.id
                    JOIN Usuario c_user ON c.usuario_id = c_user.id
                    JOIN Funcionario f ON a.profissional_id = f.id
                    JOIN Usuario f_user ON f.usuario_id = f_user.id
                    JOIN Agendamento_Servicos asv ON a.id = asv.agendamento_id
                    JOIN Servico s ON asv.servico_id = s.id
                    WHERE c.id = :cliente_id
                    GROUP BY a.id
                    ORDER BY a.data_hora DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':cliente_id' => $cliente_id]);
            
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $lista[] = $row;
            }
            
            return $lista;

        } catch (Exception $e) {
            error_log("Error in listarAgendamentosCliente: " . $e->getMessage());
            return [];
        }
    }

    public function listarAgendaPorProfissional($profissional_id)
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();
            $lista = [];

            $sql = "SELECT 
                        a.id,
                        c_user.nome AS cliente_nome,
                        GROUP_CONCAT(s.nome SEPARATOR ', ') AS servicos,
                        a.data_hora,
                        a.status
                    FROM Agendamento a
                    JOIN Cliente c ON a.cliente_id = c.id
                    JOIN Usuario c_user ON c.usuario_id = c_user.id
                    JOIN Agendamento_Servicos asv ON a.id = asv.agendamento_id
                    JOIN Servico s ON asv.servico_id = s.id
                    WHERE a.profissional_id = :profissional_id
                    GROUP BY a.id
                    ORDER BY a.data_hora ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':profissional_id' => $profissional_id]);
            
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $lista[] = $row;
            }
            
            return $lista;

        } catch (Exception $e) {
            error_log("Error in listarAgendaPorProfissional: " . $e->getMessage());
            return [];
        }
    }
    // Adicione este método dentro da classe Agendamento em Models/Agendamento.php

    /**
     * Atualiza o status de um agendamento específico.
     * @param int $idAgendamento O ID do agendamento a ser atualizado.
     * @param int $idStatus O novo ID do status (2 para Confirmado, 3 para Cancelado).
     * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário.
     */
    public function atualizarStatus($idAgendamento, $idStatus) {
        require_once 'ConexaoDB.php';
        try {
            // Usa o helper Database::getConnection() que retorna um PDO
            $pdo = Database::getConnection();

            // Mapear IDs numéricos para valores de status esperados no banco
            if ($idStatus === 2) {
                $novoStatus = 'CONFIRMADO';
            } elseif ($idStatus === 3) {
                $novoStatus = 'CANCELADO';
            } else {
                // Caso seja passado uma string ou outro valor, usa diretamente
                $novoStatus = $idStatus;
            }

            $sql = "UPDATE Agendamento SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':status' => $novoStatus,
                ':id' => $idAgendamento
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error in atualizarStatus: " . $e->getMessage());
            return false;
        }
    }
}