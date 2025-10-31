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
    public function getId() { return $this->id; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    
    // Métodos do objeto Usuario encapsulado
    public function setNome($nome) { $this->usuario->setNome($nome); }
    public function setEmail($email) { $this->usuario->setEmail($email); }
    public function setSenha($senha) { $this->usuario->setSenha($senha); }

    // --- Métodos de Banco de Dados ---

    public function inserirBD()
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();

            // 1. Insere o Usuário base
            $this->usuario_id = $this->usuario->inserirBD($pdo);
            
            if (!$this->usuario_id) {
                throw new Exception('Falha ao criar usuário');
            }

            // 2. Insere o Cliente linkando o usuario_id
            $sql = "INSERT INTO Cliente (usuario_id, telefone) VALUES (:usuario_id, :telefone)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $this->usuario_id,
                ':telefone' => $this->telefone
            ]);

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log("Error in Cliente::inserirBD: " . $e->getMessage());
            return false;
        }
    }
    
    // Carrega um cliente pelo ID do usuário
    public function carregarClientePorUsuarioId($usuario_id)
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();

            $sql = "SELECT * FROM Cliente WHERE usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);

            $row = $stmt->fetch(PDO::FETCH_OBJ);

            if ($row) {
                $this->id = $row->id;
                $this->usuario_id = $row->usuario_id;
                $this->telefone = $row->telefone;
                return true;
            }
            
            return false;

        } catch (Exception $e) {
            error_log("Error in Cliente::carregarClientePorUsuarioId: " . $e->getMessage());
            return false;
        }
    }
}