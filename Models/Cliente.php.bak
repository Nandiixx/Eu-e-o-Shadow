<?php
/*
* Modelo para a tabela 'Cliente'.
* Segue a estrutura do database.sql, ligando-se a um Usuário.
*/
class Cliente
{
    private $id;
    private $usuario_id;
    private $telefone;
    private $usuario; // Objeto do tipo Usuario

    public function __construct()
    {
        // Inclui a classe Usuario para podermos usá-la
        require_once 'Usuario.php';
        $this->usuario = new Usuario();
    }

    // --- Getters e Setters ---
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    
    // Métodos do objeto Usuario encapsulado
    public function setNome($nome) { $this->usuario->setNome($nome); }
    public function setEmail($email) { $this->usuario->setEmail($email); }
    public function setSenha($senha) { $this->usuario->setSenha($senha); }

    // --- Métodos de Banco de Dados (Agenda 11) ---

    // Insere o Usuário e depois o Cliente
    public function inserirBD()
    {
        // 1. Insere o Usuário base
        $this->usuario_id = $this->usuario->inserirBD();
        
        if ($this->usuario_id === false) {
            return false; // Falha ao criar usuário
        }

        // 2. Insere o Cliente linkando o usuario_id
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Cliente (usuario_id, telefone) VALUES (?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $this->usuario_id, $this->telefone);

        if ($stmt->execute() === TRUE) {
            $stmt->close();
            $conn->close();
            return true;
        } else {
            // TODO: Idealmente, deveria deletar o usuário criado se o cliente falhar (transação)
            $stmt->close();
            $conn->close();
            return false;
        }
    }
    
    // Carrega um cliente pelo ID do usuário
    public function carregarClientePorUsuarioId($usuario_id)
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();

        $sql = "SELECT * FROM Cliente WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);

        $stmt->execute();
        $result = $stmt->get_result();
        $r = $result->fetch_object();

        if ($r != null) {
            $this->id = $r->id;
            $this->usuario_id = $r->usuario_id;
            $this->telefone = $r->telefone;
            $stmt->close();
            $conn->close();
            return true;
        }
        
        $stmt->close();
        $conn->close();
        return false;
    }
}
?>