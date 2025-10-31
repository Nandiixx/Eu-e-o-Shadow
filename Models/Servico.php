<?php
class Servico
{
    public function listarTodos()
    {
        require_once 'ConexaoDB.php';
        try {
            $pdo = Database::getConnection();
            $lista = [];

            $sql = "SELECT id, nome, preco, duracao_minutos FROM Servico";
            $stmt = $pdo->query($sql);
            
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $lista[] = $row;
            }
            
            return $lista;

        } catch (Exception $e) {
            error_log("Error in Servico::listarTodos: " . $e->getMessage());
            return [];
        }
    }
}