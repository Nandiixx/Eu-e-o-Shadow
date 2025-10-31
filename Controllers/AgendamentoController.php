<?php
// Inclui os Modelos necessários
require_once '../Model/Agendamento.php';
require_once '../Model/Servico.php';
require_once '../Model/Funcionario.php'; // <--- ADICIONADO

class AgendamentoController
{
    // Método para exibir a página de agendamentos
    public function index()
    {
        // Requer login de Cliente
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
            header('Location: index.php');
            exit;
        }
        
        // --- Carrega dados para a View ---
        
        // 1. Carrega lista de serviços
        $servicoModel = new Servico();
        $lista_servicos = $servicoModel->listarTodos();
        
        // 2. (ATUALIZADO) Carrega lista de profissionais do Model
        $funcModel = new Funcionario();
        $lista_profissionais = $funcModel->listarTodosProfissionais(); 
        
        // 3. Carrega agendamentos existentes do cliente
        $agendamentoModel = new Agendamento();
        $cliente_id = $_SESSION['cliente_id']; // Pega da sessão
        $lista_agendamentos = $agendamentoModel->listarAgendamentosCliente($cliente_id);

        // --- Inclui a View ---
        // Passa as 3 listas para a View
        include_once '../View/agendamento.php';
    }

    // Método para salvar um novo agendamento
    public function salvar()
    {
        // Requer login de Cliente
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
            header('Location: index.php');
            exit;
        }

        $agendamento = new Agendamento();
        
        // Define os dados do agendamento
        $agendamento->setClienteId($_SESSION['cliente_id']);
        $agendamento->setProfissionalId($_POST['profissional_id']);
        $agendamento->setDataHora($_POST['dataHora']);
        
        // Adiciona os serviços (pode ser mais de um)
        if (isset($_POST['servicos_ids']) && is_array($_POST['servicos_ids'])) {
            foreach ($_POST['servicos_ids'] as $servico_id) {
                $agendamento->addServico($servico_id);
            }
        }

        // Salva no banco
        if ($agendamento->inserirBD()) {
            echo "<script>alert('Agendamento realizado com sucesso!'); window.location='index.php?acao=agendamento_mostrar';</script>";
        } else {
            echo "<script>alert('Erro ao agendar.'); window.location='index.php?acao=agendamento_mostrar';</script>";
        }
    }
    public function mostrarAgendaProfissional()
    {
        // Requer login de Profissional
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'PROFISSIONAL') {
            header('Location: index.php');
            exit;
        }
        
        $agendamentoModel = new Agendamento();
        
        // Pega o ID do profissional logado na sessão
        $funcionario_id = $_SESSION['funcionario_id']; 
        
        // Usa o novo método do Model
        $lista_agenda_profissional = $agendamentoModel->listarAgendaPorProfissional($funcionario_id);
        
        // Inclui a nova View (passando a $lista_agenda_profissional)
        include_once '../View/agenda_profissional.php';
    }
    // Adicione este método dentro da classe AgendamentoController em Controllers/AgendamentoController.php

    /**
     * Processa a mudança de status de um agendamento.
     * @param int $idAgendamento O ID do agendamento.
     * @param int $idStatus O novo ID do status.
     * @return bool Resultado da operação.
     */
    public function mudarStatusAgendamento($idAgendamento, $idStatus) {
        // O require_once já deve estar no topo do arquivo, mas caso não esteja
        // para este método, descomente a linha abaixo.
        // require_once '../Models/Agendamento.php'; 
        
        $agendamento = new Agendamento();
        
        if ($agendamento->atualizarStatus($idAgendamento, $idStatus)) {
            return true;
        } else {
            return false;
        }
    }
}
?>