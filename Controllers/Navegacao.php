<?php
// ... (includes e verificação de $acao) ...

// Estrutura switch para gerenciar as ações (Agenda 13)
switch ($acao) {
    // ... (cases de login/cadastro/logout permanecem iguais) ...

    // --- FLUXO PRINCIPAL (APÓS LOGIN) ---
    case 'inicio':
        // $userController->mostrarInicio(); // Linha antiga
        $userController->direcionarDashboard(); // <--- NOVA LINHA
        break;
    
    // --- FLUXO DE AGENDAMENTO (CLIENTE) ---
    case 'agendamento_mostrar':
        $agendamentoController->index(); // (Visão do Cliente)
        break;
    
    case 'agendamento_salvar':
        $agendamentoController->salvar(); // (Ação do Cliente)
        break;
        
    // --- (NOVO) FLUXO DE AGENDA (PROFISSIONAL) ---
    case 'agenda_profissional_mostrar':
        $agendamentoController->mostrarAgendaProfissional();
        break;

    // Adicione estes cases ao switch em Controllers/Navegacao.php

    case 'confirmar':
        require_once 'Controllers/AgendamentoController.php';
        $controller = new AgendamentoController();
        
        if (isset($_GET['id'])) {
            $idAgendamento = $_GET['id'];
            $idStatusConfirmado = 2; // ID 'CONFIRMADO' do seu database.sql
            
            if ($controller->mudarStatusAgendamento($idAgendamento, $idStatusConfirmado)) {
                echo "<script>alert('Agendamento confirmado com sucesso!'); window.location.href='index.php?acao=agenda_profissional';</script>";
            } else {
                echo "<script>alert('Erro ao confirmar o agendamento.'); window.location.href='index.php?acao=agenda_profissional';</script>";
            }
        }
        break;

    case 'cancelar':
        require_once 'Controllers/AgendamentoController.php';
        $controller = new AgendamentoController();
        
        if (isset($_GET['id'])) {
            $idAgendamento = $_GET['id'];
            $idStatusCancelado = 3; // ID 'CANCELADO' do seu database.sql
            
            if ($controller->mudarStatusAgendamento($idAgendamento, $idStatusCancelado)) {
                echo "<script>alert('Agendamento cancelado.'); window.location.href='index.php?acao=agenda_profissional';</script>";
            } else {
                echo "<script>alert('Erro ao cancelar o agendamento.'); window.location.href='index.php?acao=agenda_profissional';</script>";
            }
        }
        break;

    // Ação padrão
    default:
        $userController->mostrarLogin();
        break;
}
?>