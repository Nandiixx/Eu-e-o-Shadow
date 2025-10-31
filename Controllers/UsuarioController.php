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

        // Carrega o usuário base
        require_once '../Models/Usuario.php';
        $usuario = new Usuario();

        // 1. Verifica se o e-mail existe
        if ($usuario->carregarUsuarioPorEmail($email)) {
            
            // 2. Verifica se a senha está correta
            // (Estou usando o método verificarSenha() do seu snippet)
            if ($usuario->verificarSenha($senha)) {
                
                // (O session_start() já deve estar no topo do seu arquivo UsuarioController.php)

                // Carrega os models específicos
                require_once '../Models/Cliente.php';
                require_once '../Models/Funcionario.php';

                $cliente = new Cliente();
                $funcionario = new Funcionario(); 

                // Salva dados básicos do usuário na sessão
                $_SESSION['usuario_id'] = $usuario->getId();
                $_SESSION['usuario_nome'] = $usuario->getNome();
                
                // Pega o cargo do usuário (essencial para o redirecionamento)
                // Tenta múltiplas formas de obter o cargo para compatibilidade com o Model
                $idCargo = null;
                if (method_exists($usuario, 'getIdCargo')) {
                    $idCargo = $usuario->getIdCargo();
                } elseif (method_exists($usuario, 'getCargoId')) {
                    $idCargo = $usuario->getCargoId();
                } elseif (method_exists($usuario, 'getCargo')) {
                    $idCargo = $usuario->getCargo();
                } elseif (isset($usuario->id_cargo)) {
                    $idCargo = $usuario->id_cargo;
                } elseif (isset($usuario->cargo_id)) {
                    $idCargo = $usuario->cargo_id;
                } elseif (isset($usuario->cargo)) {
                    $idCargo = $usuario->cargo;
                }

                // Se não foi possível determinar o cargo, aborta com mensagem
                if (empty($idCargo)) {
                    session_destroy();
                    echo "<script>alert('Erro: Tipo de usuário não reconhecido ou dados inconsistentes.'); window.location='../index.php?acao=login_mostrar';</script>";
                    exit;
                }

                // --- LÓGICA DE REDIRECIONAMENTO CORRIGIDA ---

                // 3. Verifica o cargo e redireciona
                
                // É Administrador (Proprietário ou Gerente)?
                if ($idCargo == 1 || $idCargo == 2) {
                    $_SESSION['usuario_tipo'] = 'ADMIN';
                    // Redireciona para o painel admin que criamos
                    header('Location: ../index.php?acao=inicio_admin');
                    exit;
                } 
                
                // É Cliente? (Cargo 3 E existe na tabela cliente)
                elseif ($idCargo == 3 && $cliente->carregarClientePorUsuarioId($usuario->getId())) {
                    $_SESSION['usuario_tipo'] = 'CLIENTE';
                    $_SESSION['cliente_id'] = $cliente->getId();
                    header('Location: ../index.php?acao=inicio_cliente');
                    exit;
                } 
                
                // É Profissional? (Cargo 4 E existe na tabela funcionario)
                elseif ($idCargo == 4 && $funcionario->carregarFuncionarioPorUsuarioId($usuario->getId())) {
                    $_SESSION['usuario_tipo'] = 'PROFISSIONAL';
                    $_SESSION['funcionario_id'] = $funcionario->getId();
                    header('Location: ../index.php?acao=inicio_profissional');
                    exit;
                } 
                
                // Se não for nenhum (ex: dados inconsistentes no DB)
                else {
                    session_destroy();
                    echo "<script>alert('Erro: Tipo de usuário não reconhecido ou dados inconsistentes.'); window.location='../index.php?acao=login_mostrar';</script>";
                    exit;
                }

            } else {
                echo "<script>alert('E-mail ou senha incorretos'); window.location='../index.php?acao=login_mostrar';</script>";
            }
        } else {
            echo "<script>alert('E-mail ou senha incorretos'); window.location='../index.php?acao=login_mostrar';</script>";
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