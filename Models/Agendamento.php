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
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Inicia transação
        $conn->begin_transaction();

        try {
            // 1. Insere na tabela Agendamento
            $sql = "INSERT INTO Agendamento (cliente_id, profissional_id, data_hora, status) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $status = 'AGENDADO'; // Default
            $stmt->bind_param("iiss", $this->cliente_id, $this->profissional_id, $this->data_hora, $status);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao agendar.");
            }
            
            $agendamento_id = $conn->insert_id;
            $stmt->close();

            // 2. Insere na tabela Agendamento_Servicos
            $sql_serv = "INSERT INTO Agendamento_Servicos (agendamento_id, servico_id) VALUES (?, ?)";
            $stmt_serv = $conn->prepare($sql_serv);

            foreach ($this->servicos as $servico_id) {
                $stmt_serv->bind_param("ii", $agendamento_id, $servico_id);
                if (!$stmt_serv->execute()) {
                    throw new Exception("Erro ao ligar serviços.");
                }
            }
            
            $stmt_serv->close();
            
            // Se tudo deu certo, comita
            $conn->commit();
            $conn->close();
            return true;

        } catch (Exception $e) {
            // Se algo deu errado, faz rollback
            $conn->rollback();
            $conn->close();
            return false;
        }
    }
    
    public function listarAgendamentosCliente($cliente_id)
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();
        $lista = [];

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

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
                WHERE c.id = ?
                GROUP BY a.id
                ORDER BY a.data_hora DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
             while($r = $result->fetch_object()) {
                $lista[] = $r;
            }
        }
        
        $stmt->close();
        $conn->close();
        return $lista;
    }
    public function listarAgendaPorProfissional($profissional_id)
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();
        $lista = [];

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query similar à do cliente, mas filtrando por a.profissional_id
        // E trazendo o nome do Cliente
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
                WHERE a.profissional_id = ?  -- Filtro principal
                GROUP BY a.id
                ORDER BY a.data_hora ASC"; // Profissionais preferem ver os próximos

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $profissional_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
             while($r = $result->fetch_object()) {
                $lista[] = $r;
            }
        }
        
        $stmt->close();
        $conn->close();
        return $lista;
    }
}
?>