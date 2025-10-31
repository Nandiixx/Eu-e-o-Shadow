<?php
/*
* Modelo para a tabela 'Funcionario', que representa o Profissional.
* Agora inclui métodos para inserir no banco usando PDO.
*/
require_once 'Usuario.php';

class Funcionario
{
    private $id;
    private $usuario_id;
    private $cargo;
    private $usuario; // Objeto do tipo Usuario

    public function __construct()
    {
        $this->usuario = new Usuario();
    }

    public function getId() { return $this->id; }

    // --- Getters e Setters ---
    public function setCargo($cargo) { $this->cargo = $cargo; }
    public function getCargo() { return $this->cargo; }
    
    // Métodos do objeto Usuario encapsulado
    public function setNome($nome) { $this->usuario->setNome($nome); }
    public function setEmail($email) { $this->usuario->setEmail($email); }
    public function setSenha($senha) { $this->usuario->setSenha($senha); }

    
    // --- Métodos de Banco de Dados ---

    /**
     * Insere o Usuário e depois o Funcionario
     * @return bool Retorna true se a inserção foi bem sucedida, false caso contrário
     */
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

            // 2. Insere o Funcionario linkando o usuario_id
            $sql = "INSERT INTO Funcionario (usuario_id, cargo) VALUES (:usuario_id, :cargo)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $this->usuario_id,
                ':cargo' => $this->cargo
            ]);

            $this->id = $pdo->lastInsertId();
            
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log("Error in Funcionario::inserirBD: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Carrega dados do funcionário baseado no ID do usuário
     * @param int $usuario_id ID do usuário associado
     * @return bool true se encontrou o funcionário, false caso contrário
     */
    public function carregarFuncionarioPorUsuarioId($usuario_id)
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();

            $sql = "SELECT * FROM Funcionario WHERE usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);

            $row = $stmt->fetch(PDO::FETCH_OBJ);

            if ($row) {
                $this->id = $row->id;
                $this->usuario_id = $row->usuario_id;
                $this->cargo = $row->cargo;
                return true;
            }
            
            return false;

        } catch (Exception $e) {
            error_log("Error in Funcionario::carregarFuncionarioPorUsuarioId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lista todos os profissionais cadastrados
     * @return array Lista de profissionais com id e nome
     */
    public function listarTodosProfissionais()
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();
            $lista = [];

            $sql = "SELECT f.id, u.nome 
                    FROM Funcionario f 
                    JOIN Usuario u ON f.usuario_id = u.id
                    ORDER BY u.nome";
                    
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $lista[] = $row;
            }

            return $lista;

        } catch (Exception $e) {
            error_log("Error in Funcionario::listarTodosProfissionais: " . $e->getMessage());
            return [];
        }
    }
}
?>