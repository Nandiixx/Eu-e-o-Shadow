<?php
/*
* Modelo para a tabela 'Funcionario', que representa o Profissional.
*/
require_once 'Usuario.php';

class Funcionario
{
    private $id;
    private $usuario_id;
    private $cargo;
    private $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    public function getId() { return $this->id; }

    /**
     * Carrega dados do funcionário baseado no ID do usuário base.
     */
    public function carregarFuncionarioPorUsuarioId($usuario_id)
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Funcionario WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);

        $stmt->execute();
        $result = $stmt->get_result();
        $r = $result->fetch_object();

        if ($r != null) {
            $this->id = $r->id;
            $this->usuario_id = $r->usuario_id;
            $this->cargo = $r->cargo;
            $stmt->close();
            $conn->close();
            return true;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }
    
    /**
     * Lista todos os usuários que são funcionários (profissionais)
     * para preencher o dropdown de agendamento do cliente.
     */
    public function listarTodosProfissionais()
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();
        $lista = [];

        // Busca o ID do funcionário e o NOME do usuário associado
        $sql = "SELECT f.id, u.nome 
                FROM Funcionario f
                JOIN Usuario u ON f.usuario_id = u.id";
                // WHERE f.cargo = 'Cabeleireiro' -- Você pode filtrar por cargo se desejar

        $re = $conn->query($sql);
        if ($re->num_rows > 0) {
             while($r = $re->fetch_object()) {
                $lista[] = $r;
            }
        }
        $conn->close();
        return $lista;
    }
}
?>