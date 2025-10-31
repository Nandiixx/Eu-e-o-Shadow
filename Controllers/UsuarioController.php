<?php
// Inclui os Modelos necessários
require_once '../Model/Usuario.php';
require_once '../Model/Cliente.php';
require_once '../Model/Funcionario.php'; // <--- ADICIONADO

class UsuarioController
{
    // ... (mostrarLogin, mostrarCadastro, salvarCadastro permanecem iguais) ...
    // ... (COLE OS MÉTODOS ANTERIORES AQUI) ...

    public function autenticar()
    {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $usuario = new Usuario();
        
        if ($usuario->carregarUsuarioPorEmail($email)) {
            
            if ($usuario->verificarSenha($senha)) {
                
                // --- AQUI ESTÁ A MUDANÇA ---
                // Verifica o TIPO de usuário
                $cliente = new Cliente();
                $funcionario = new Funcionario(); // Instancia o novo model
                
                $_SESSION['usuario_id'] = $usuario->getId();
                $_SESSION['usuario_nome'] = $usuario->getNome();

                // Tenta carregar como Cliente
                if ($cliente->carregarClientePorUsuarioId($usuario->getId())) {
                    $_SESSION['usuario_tipo'] = 'CLIENTE';
                    $_SESSION['cliente_id'] = $cliente->getId(); // Armazena o ID do cliente
                } 
                // Se não for cliente, tenta carregar como Profissional (Funcionário)
                elseif ($funcionario->carregarFuncionarioPorUsuarioId($usuario->getId())) {
                    $_SESSION['usuario_tipo'] = 'PROFISSIONAL';
                    $_SESSION['funcionario_id'] = $funcionario->getId(); // Armazena o ID do funcionário
                } 
                // Se não for nenhum dos dois, é um usuário base sem função
                else {
                    session_destroy();
                    echo "<script>alert('Tipo de usuário não reconhecido.'); window.location='index.php?acao=login_mostrar';</script>";
                    exit;
                }
                
                // Redireciona para a página inicial (que será tratada pelo Navegacao.php)
                header('Location: index.php?acao=inicio');
                exit;

            } else {
                echo "<script>alert('E-mail ou senha incorretos'); window.location='index.php?acao=login_mostrar';</script>";
            }
        } else {
            echo "<script>alert('E-mail ou senha incorretos'); window.location='index.php?acao=login_mostrar';</script>";
        }
    }

    /**
     * (NOVA FUNÇÃO)
     * Verifica o tipo de usuário na sessão e carrega o dashboard correto.
     */
    public function direcionarDashboard()
    {
        if (!$this->checkLogin()) {
            header('Location: index.php?acao=login_mostrar');
            exit;
        }

        // Direciona com base no tipo de usuário salvo na sessão
        if (isset($_SESSION['usuario_tipo'])) {
            switch ($_SESSION['usuario_tipo']) {
                case 'CLIENTE':
                    // Mostra o dashboard padrão do cliente
                    include_once '../View/inicio_cliente.php'; 
                    break;
                case 'PROFISSIONAL':
                    // Mostra o novo dashboard do profissional
                    include_once '../View/inicio_profissional.php';
                    break;
                default:
                    $this->logout(); // Tipo desconhecido, faz logout
                    break;
            }
        } else {
            $this->logout(); // Sessão corrompida, faz logout
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: index.php?acao=login_mostrar');
        exit;
    }
    
    public function checkLogin()
    {
        return (isset($_SESSION['usuario_id']));
    }
}
?>