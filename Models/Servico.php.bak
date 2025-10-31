<?php
class Servico
{
    public function listarTodos()
    {
        require_once 'ConexaoBD.php';
        $con = new ConexaoBD();
        $conn = $con->conectar();
        $lista = [];

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT id, nome, preco, duracao_minutos FROM Servico";
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