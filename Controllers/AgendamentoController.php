<?php
// Inicia a sessão se já não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui os Modelos necessários
require_once '../Models/Agendamento.php';
require_once '../Models/Servico.php';
require_once '../Models/Funcionario.php';

class AgendamentoController
{
    // Método para exibir a página de agendamentos
    public function index()
    {
        // Requer login de Cliente
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
            header('Location: ../index.php?acao=login_mostrar');
            exit;
        }
        
        // --- Carrega dados para a View ---
        
        // 1. Carrega lista de serviços
        $servicoModel = new Servico();
        $lista_servicos = $servicoModel->listarTodos();
        
        // 2. Carrega lista de profissionais
        $funcModel = new Funcionario();
        $lista_profissionais = $funcModel->listarTodosProfissionais(); 
        
        // 3. Carrega agendamentos existentes do cliente
        $agendamentoModel = new Agendamento();
        $cliente_id = $_SESSION['cliente_id']; // Pega da sessão
        $lista_agendamentos = $agendamentoModel->listarAgendamentosCliente($cliente_id);

        // --- Inclui a View ---
        // Passa as 3 listas para a View
        include_once '../Views/agendamento.php';
    }

    /**
     * Método para salvar um novo agendamento
     * (MÉTODO ATUALIZADO COM VALIDAÇÃO)
     */
    public function salvar()
    {
        // Requer login de Cliente
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
            header('Location: ../index.php?acao=login_mostrar');
            exit;
        }

        $erros = []; // Array para armazenar os erros de validação

        // 1. Validação dos dados de entrada (POST)
        if (empty($_POST['profissional_id'])) {
            $erros[] = "Você deve selecionar um profissional.";
        }
        if (empty($_POST['dataHora'])) {
            $erros[] = "Você deve selecionar uma data e hora.";
        }
        if (empty($_POST['servicos_ids']) || !is_array($_POST['servicos_ids'])) {
            $erros[] = "Você deve selecionar pelo menos um serviço.";
        }
        // (Você pode adicionar mais validações, ex: verificar se a data é no futuro)

        // 2. Se houver erros, armazena na sessão e redireciona de volta
        if (!empty($erros)) {
            $_SESSION['erros_agendamento'] = $erros;
            header('Location: ../index.php?acao=agendamento_mostrar');
            exit;
        }

        // 3. Se a validação passou, continua com o processo
        try {
            $agendamento = new Agendamento();
            
            // Define os dados do agendamento
            $agendamento->setClienteId($_SESSION['cliente_id']);
            $agendamento->setProfissionalId((int)$_POST['profissional_id']);
            $agendamento->setDataHora($_POST['dataHora']);
            
            // Adiciona os serviços (pode ser mais de um)
            foreach ($_POST['servicos_ids'] as $servico_id) {
                $agendamento->addServico((int)$servico_id);
            }

            // Salva no banco
            if ($agendamento->inserirBD()) {
                $_SESSION['sucesso_agendamento'] = "Agendamento realizado com sucesso!";
                header('Location: ../index.php?acao=agendamento_mostrar');
                exit;
            } else {
                $_SESSION['erros_agendamento'] = ["Erro ao salvar o agendamento no banco de dados."];
                header('Location: ../index.php?acao=agendamento_mostrar');
                exit;
            }
        } catch (Exception $e) {
            // Captura exceções (ex: erro de conexão ou SQL)
            $_SESSION['erros_agendamento'] = ["Erro inesperado no servidor: " . $e->getMessage()];
            header('Location: ../index.php?acao=agendamento_mostrar');
            exit;
        }
    }
    
    /**
     * Mostra a agenda do profissional
     * (Seu método original, mantido como está)
     */
    public function mostrarAgendaProfissional()
    {
        // Requer login de Profissional
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'PROFISSIONAL') {
            header('Location: ../index.php?acao=login_mostrar');
            exit;
        }
        
        $agendamentoModel = new Agendamento();
        
        // Pega o ID do profissional logado na sessão
        $funcionario_id = $_SESSION['funcionario_id']; 
        
        // Usa o novo método do Model
        $lista_agenda_profissional = $agendamentoModel->listarAgendaPorProfissional($funcionario_id);
        
        // Inclui a nova View
        include_once '../Views/agenda_profissional.php';
    }

    /**
     * Processa a mudança de status de um agendamento.
     * (Seu método original, mantido como está)
     */
    public function mudarStatusAgendamento($idAgendamento, $idStatus) {
        $agendamento = new Agendamento();
        
        if ($agendamento->atualizarStatus($idAgendamento, $idStatus)) {
            return true;
        } else {
            return false;
        }
    }
}
?>