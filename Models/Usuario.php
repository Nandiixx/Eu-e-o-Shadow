<?php
/*
* Modelo para a tabela 'Usuario'.
* Utiliza password_hash para segurança das senhas.
*/
class Usuario
{
    private $id;
    private $nome;
    private $email;
    private $senha_hash;

    // --- Getters e Setters ---
    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getNome() { return $this->nome; }
    public function setEmail($email) { $this->email = $email; }
    public function getEmail() { return $this->email; }
    
    public function setSenha($senha) {
        $this->senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    }
    
    public function getSenhaHash() { return $this->senha_hash; }

    // --- Métodos de Banco de Dados ---

    /**
     * Insere um novo usuário no banco de dados
     * @param PDO $pdo Conexão PDO ativa (opcional, se não fornecida, cria nova conexão)
     * @return int|false ID do usuário inserido ou false em caso de erro
     */
    public function inserirBD($pdo = null)
    {
        $needsNewConnection = $pdo === null;
        
        try {
            if ($needsNewConnection) {
                require_once 'ConexaoDB.php';
                $pdo = Database::getConnection();
                $pdo->beginTransaction();
            }

            $sql = "INSERT INTO Usuario (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $this->nome,
                ':email' => $this->email,
                ':senha_hash' => $this->senha_hash
            ]);

            $this->id = $pdo->lastInsertId();

            if ($needsNewConnection) {
                $pdo->commit();
            }

            return $this->id;

        } catch (Exception $e) {
            if ($needsNewConnection && isset($pdo)) {
                $pdo->rollBack();
            }
            error_log("Error in Usuario::inserirBD: " . $e->getMessage());
            return false;
        }
    }

    public function carregarUsuarioPorEmail($email)
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();

            $sql = "SELECT * FROM Usuario WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);

            $row = $stmt->fetch(PDO::FETCH_OBJ);

            if ($row) {
                $this->id = $row->id;
                $this->nome = $row->nome;
                $this->email = $row->email;
                $this->senha_hash = $row->senha_hash;
                return true;
            }
            
            return false;

        } catch (Exception $e) {
            error_log("Error in Usuario::carregarUsuarioPorEmail: " . $e->getMessage());
            return false;
        }
    }

    public function verificarSenha($senha)
    {
        return password_verify($senha, $this->senha_hash);
    }
}