<?php
require_once __DIR__ . '/../Models/AgendamentoModel.php';

class AgendamentoController {
    private $model;

    public function __construct() {
        $this->model = new AgendamentoModel();
    }

    public function index() {
        try {
            $filtros = [
                'cliente_id' => $_GET['cliente_id'] ?? null,
                'profissional_id' => $_GET['profissional_id'] ?? null,
                'data' => $_GET['data'] ?? null
            ];
            
            $agendamentos = $this->model->listarTodos($filtros);
            return [
                'success' => true,
                'data' => $agendamentos
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function criar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método não permitido');
            }

            $dados = json_decode(file_get_contents('php://input'), true);
            
            // Validação básica
            if (empty($dados['cliente_id']) || empty($dados['profissional_id']) || 
                empty($dados['servico_id']) || empty($dados['data_hora'])) {
                throw new Exception('Dados incompletos');
            }

            // Verifica disponibilidade
            if (!$this->model->verificarDisponibilidade($dados['profissional_id'], $dados['data_hora'])) {
                throw new Exception('Horário não disponível');
            }

            $id = $this->model->criar($dados);
            return [
                'success' => true,
                'message' => 'Agendamento criado com sucesso',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function atualizar($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                throw new Exception('Método não permitido');
            }

            $dados = json_decode(file_get_contents('php://input'), true);
            
            if (empty($dados)) {
                throw new Exception('Dados inválidos');
            }

            // Se estiver atualizando a data/hora, verifica disponibilidade
            if (isset($dados['data_hora']) && isset($dados['profissional_id'])) {
                if (!$this->model->verificarDisponibilidade($dados['profissional_id'], $dados['data_hora'])) {
                    throw new Exception('Horário não disponível');
                }
            }

            $success = $this->model->atualizar($id, $dados);
            return [
                'success' => $success,
                'message' => $success ? 'Agendamento atualizado com sucesso' : 'Nenhuma alteração realizada'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function excluir($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Método não permitido');
            }

            $success = $this->model->excluir($id);
            return [
                'success' => $success,
                'message' => $success ? 'Agendamento excluído com sucesso' : 'Agendamento não encontrado'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function visualizar($id) {
        try {
            $agendamento = $this->model->buscarPorId($id);
            if (!$agendamento) {
                throw new Exception('Agendamento não encontrado');
            }

            return [
                'success' => true,
                'data' => $agendamento
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
