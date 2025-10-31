<?php
// Inicia a sessão se já não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui os Modelos necessários
require_once '../Models/Usuario.php';
require_once '../Models/Cliente.php';
require_once '../Models/Funcionario.php';

class UsuarioController
{
    /**
     * Exibe a página de login.
     */
    public function mostrarLogin()
    {
        // Verifica se há uma mensagem de sucesso (ex: vindo do cadastro)
        $sucesso = $_GET['status'] ?? null;
        require_once '../Views/login.php';
    }

    /**
     * Exibe a página de cadastro.
     */
    public function mostrarCadastro()
    {
        require_once '../Views/cadastrar.php';
    }

    /**
     * (NOVO MÉTODO)
     * Processa o formulário de cadastro de cliente.
     * Valida os dados e, se corretos, salva no banco.
     * Também exibe a view de cadastro.
     */
    public function salvarCadastroCliente()
    {
        $erro = null;

        // Processa o formulário quando enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validações
            if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha']) ||
                empty($_POST['confirma_senha']) || empty($_POST['telefone'])) {
                $erro = "Todos os campos obrigatórios devem ser preenchidos.";
            } else if ($_POST['senha'] !== $_POST['confirma_senha']) {
                $erro = "As senhas não coincidem.";
            } else if (strlen($_POST['senha']) < 6) {
                $erro = "A senha deve ter pelo menos 6 caracteres.";
            } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $erro = "E-mail inválido.";
            } else {
                try {
                    // Tenta cadastrar
                    $cliente = new Cliente();
                    $cliente->setNome($_POST['nome']);
                    $cliente->setEmail($_POST['email']);
                    $cliente->setSenha($_POST['senha']); // O hash é feito no Model
                    $cliente->setTelefone($_POST['telefone']);

                    // Verificação de e-mail existente (assumindo que seu Model Cliente tem esse método)
                    // Se não tiver, o BD provavelmente vai falhar por 'UNIQUE constraint'
                    // if ($cliente->emailJaExiste()) {
                    //     $erro = "Este e-mail já está em uso.";
                    // } else
                    
                    if ($cliente->inserirBD()) {
                        // Redireciona para o login com mensagem de sucesso
                        header("Location: ../index.php?acao=login_mostrar&status=sucesso");
                        exit;
                    } else {
                        $erro = "Erro ao cadastrar. Por favor, tente novamente.";
                    }
                } catch (Exception $e) {
                    // Captura erros do banco de dados (ex: e-mail duplicado)
                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                        $erro = "Este e-mail ou telefone já está cadastrado.";
                    } else {
                        $erro = "Erro no servidor: " . $e->getMessage();
                    }
                }
            }
        }

        // Exibe a página de cadastro (seja no primeiro acesso ou se der erro)
        // A variável $erro será usada na view
        require_once '../Views/cadastrar.php';
    }

    /**
     * Autentica o usuário e redireciona.
     * (Seu método original, mantido como está)
     */
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
            if ($usuario->verificarSenha($senha)) {
                
                // Carrega os models específicos
                require_once '../Models/Cliente.php';
                require_once '../Models/Funcionario.php';

                $cliente = new Cliente();
                $funcionario = new Funcionario(); 

                // Salva dados básicos do usuário na sessão
                $_SESSION['usuario_id'] = $usuario->getId();
                $_SESSION['usuario_nome'] = $usuario->getNome();
                
                // Pega o cargo do usuário
                $idCargo = null;
                if (method_exists($usuario, 'getIdCargo')) {
                    $idCargo = $usuario->getIdCargo();
                } elseif (method_exists($usuario, 'getCargoId')) {
                    $idCargo = $usuario->getCargoId();
                } elseif (isset($usuario->id_cargo)) {
                    $idCargo = $usuario->id_cargo;
                }

                if (empty($idCargo)) {
                    session_destroy();
                    echo "<script>alert('Erro: Tipo de usuário não reconhecido.'); window.location='../index.php?acao=login_mostrar';</script>";
                    exit;
                }

                // --- LÓGICA DE REDIRECIONAMENTO ---
                
                // É Administrador (Proprietário ou Gerente)?
                if ($idCargo == 1 || $idCargo == 2) {
                    $_SESSION['usuario_tipo'] = 'ADMIN';
                    header('Location: ../index.php?acao=inicio_admin'); // Assumindo que esta rota exista
                    exit;
                } 
                
                // É Cliente? (Cargo 3)
                elseif ($idCargo == 3 && $cliente->carregarClientePorUsuarioId($usuario->getId())) {
                    $_SESSION['usuario_tipo'] = 'CLIENTE';
                    $_SESSION['cliente_id'] = $cliente->getId();
                    header('Location: ../index.php?acao=inicio'); // Vai para o dashboard
                    exit;
                } 
                
                // É Profissional? (Cargo 4)
                elseif ($idCargo == 4 && $funcionario->carregarFuncionarioPorUsuarioId($usuario->getId())) {
                    $_SESSION['usuario_tipo'] = 'PROFISSIONAL';
                    $_SESSION['funcionario_id'] = $funcionario->getId();
                    header('Location: ../index.php?acao=inicio'); // Vai para o dashboard
                    exit;
                } 
                
                else {
                    session_destroy();
                    echo "<script>alert('Erro: Dados de usuário inconsistentes.'); window.location='../index.php?acao=login_mostrar';</script>";
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
     * Verifica o tipo de usuário na sessão e carrega o dashboard correto.
     * (Seu método original, mantido como está)
     */
    public function direcionarDashboard()
    {
        if (!$this->checkLogin()) {
            header('Location: ../index.php?acao=login_mostrar');
            exit;
        }

        // Direciona com base no tipo de usuário salvo na sessão
        if (isset($_SESSION['usuario_tipo'])) {
            switch ($_SESSION['usuario_tipo']) {
                case 'CLIENTE':
                    // Mostra o dashboard padrão do cliente
                    // (Corrigido o caminho para a View)
                    include_once '../Views/inicio_cliente.php'; 
                    break;
                case 'PROFISSIONAL':
                    // Mostra o novo dashboard do profissional
                    // (Corrigido o caminho para a View)
                    include_once '../Views/inicio_profissional.php';
                    break;
                default:
                    $this->logout(); // Tipo desconhecido, faz logout
                    break;
            }
        } else {
            $this->logout(); // Sessão corrompida, faz logout
        }
    }

    /**
     * Faz logout do usuário.
     * (Seu método original, mantido como está)
     */
    public function logout()
    {
        session_destroy();
        header('Location: ../index.php?acao=login_mostrar');
        exit;
    }
    
    /**
     * Verifica se o usuário está logado.
     * (Seu método original, mantido como está)
     */
    public function checkLogin()
    {
        return (isset($_SESSION['usuario_id']));
    }
}
?>