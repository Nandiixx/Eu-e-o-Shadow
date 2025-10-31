<?php
/*
* Modelo para a tabela 'Usuario'.
* Utiliza password_hash da Agenda 15 para segurança.
*/
class Usuario
{
    private $id;
    private $nome;
    private $email;
    private $senha_hash;

    // --- Getters e Setters (Agenda 09) ---
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getNome() { return $this->nome; }
    public function setEmail($email) { $this->email = $email; }
    public function getEmail() { return $this->email; }
    
    // Método de segurança da Agenda 15
    public function setSenha($senha) {
        $this->senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    }
    
    public function getSenhaHash() { return $this->senha_hash; }

    // --- Métodos de Banco de Dados (Agenda 11) ---

    public function inserirBD()
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Usuario (nome, email, senha_hash) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $this->nome, $this->email, $this->senha_hash);

        if ($stmt->execute() === TRUE) {
            $this->id = $conn->insert_id; // Pega o ID gerado
            $stmt->close();
            $conn->close();
            return $this->id; // Retorna o ID do novo usuário
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    public function carregarUsuarioPorEmail($email)
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM Usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        $stmt->execute();
        $result = $stmt->get_result();
        $r = $result->fetch_object();

        if ($r != null) {
            $this->id = $r->id;
            $this->nome = $r->nome;
            $this->email = $r->email;
            $this->senha_hash = $r->senha_hash;
            $stmt->close();
            $conn->close();
            return true;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    // Verifica a senha usando a técnica da Agenda 15
    public function verificarSenha($senha)
    {
        return password_verify($senha, $this->senha_hash);
    }
}
?>