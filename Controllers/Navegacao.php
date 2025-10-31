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

    // Ação padrão
    default:
        $userController->mostrarLogin();
        break;
}
?>