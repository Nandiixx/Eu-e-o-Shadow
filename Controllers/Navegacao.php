<?php
// Inicia a sessão para todas as requisições
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui os controllers principais
require_once 'Controllers/UsuarioController.php';
require_once 'Controllers/AgendamentoController.php';

// Instancia os controllers
$userController = new UsuarioController();
$agendamentoController = new AgendamentoController();

// Define a ação padrão (mostrar login) se nenhuma for especificada
$acao = $_GET['acao'] ?? 'login_mostrar';

// Estrutura switch para gerenciar as ações (Roteamento)
switch ($acao) {

    // --- FLUXO DE AUTENTICAÇÃO E CADASTRO ---
    case 'login_mostrar':
        $userController->mostrarLogin();
        break;

    case 'autenticar': // Ação do formulário de login
        $userController->autenticar();
        break;

    case 'cadastro_mostrar':
        $userController->mostrarCadastro();
        break;
    
    case 'salvar_cliente': // Ação do formulário de cadastro
        $userController->salvarCadastroCliente();
        break;

    case 'logout':
        $userController->logout();
        break;

    // --- FLUXO PRINCIPAL (APÓS LOGIN) ---
    case 'inicio':
        $userController->direcionarDashboard(); // Direciona para o dashboard correto
        break;
    
    // --- FLUXO DE AGENDAMENTO (CLIENTE) ---
    case 'agendamento_mostrar':
        $agendamentoController->index(); // (Visão do Cliente)
        break;
    
    case 'agendamento_salvar':
        $agendamentoController->salvar(); // (Ação do Cliente)
        break;
        
    // --- FLUXO DE AGENDA (PROFISSIONAL) ---
    case 'agenda_profissional_mostrar':
        $agendamentoController->mostrarAgendaProfissional();
        break;

    case 'confirmar':
        if (isset($_GET['id'])) {
            $idAgendamento = $_GET['id'];
            $idStatusConfirmado = 2; // ID 'CONFIRMADO'
            
            if ($agendamentoController->mudarStatusAgendamento($idAgendamento, $idStatusConfirmado)) {
                echo "<script>alert('Agendamento confirmado!'); window.location.href='../index.php?acao=agenda_profissional_mostrar';</script>";
            } else {
                echo "<script>alert('Erro ao confirmar.'); window.location.href='../index.php?acao=agenda_profissional_mostrar';</script>";
            }
        }
        break;

    case 'cancelar':
         if (isset($_GET['id'])) {
            $idAgendamento = $_GET['id'];
            $idStatusCancelado = 3; // ID 'CANCELADO'
            
            if ($agendamentoController->mudarStatusAgendamento($idAgendamento, $idStatusCancelado)) {
                echo "<script>alert('Agendamento cancelado.'); window.location.href='../index.php?acao=agenda_profissional_mostrar';</script>";
            } else {
                echo "<script>alert('Erro ao cancelar.'); window.location.href='../index.php?acao=agenda_profissional_mostrar';</script>";
            }
        }
        break;

    // Ação padrão
    default:
        $userController->mostrarLogin();
        break;
}
?>