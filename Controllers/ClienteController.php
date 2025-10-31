<?php
require_once __DIR__ . '/../Models/ClienteModel.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    public function index() {
        try {
            $filtros = [
                'nome' => $_GET['nome'] ?? null,
                'email' => $_GET['email'] ?? null,
                'cpf' => $_GET['cpf'] ?? null
            ];
            
            $clientes = $this->model->listarTodos($filtros);
            return [
                'success' => true,
                'data' => $clientes
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
            if (empty($dados['nome']) || empty($dados['email']) || 
                empty($dados['telefone']) || empty($dados['cpf'])) {
                throw new Exception('Dados incompletos');
            }

            // Validar email
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }

            // Validar CPF
            if (!$this->model->validarCPF($dados['cpf'])) {
                throw new Exception('CPF inválido');
            }

            // Limpar e formatar dados
            $dados['nome'] = trim($dados['nome']);
            $dados['email'] = strtolower(trim($dados['email']));
            $dados['cpf'] = preg_replace('/[^0-9]/', '', $dados['cpf']);
            $dados['telefone'] = preg_replace('/[^0-9]/', '', $dados['telefone']);

            $id = $this->model->criar($dados);
            return [
                'success' => true,
                'message' => 'Cliente cadastrado com sucesso',
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

            // Validações específicas para cada campo presente
            if (isset($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }

            if (isset($dados['cpf']) && !$this->model->validarCPF($dados['cpf'])) {
                throw new Exception('CPF inválido');
            }

            // Limpar e formatar dados
            if (isset($dados['nome'])) $dados['nome'] = trim($dados['nome']);
            if (isset($dados['email'])) $dados['email'] = strtolower(trim($dados['email']));
            if (isset($dados['cpf'])) $dados['cpf'] = preg_replace('/[^0-9]/', '', $dados['cpf']);
            if (isset($dados['telefone'])) $dados['telefone'] = preg_replace('/[^0-9]/', '', $dados['telefone']);

            $success = $this->model->atualizar($id, $dados);
            return [
                'success' => $success,
                'message' => $success ? 'Cliente atualizado com sucesso' : 'Nenhuma alteração realizada'
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
                'message' => $success ? 'Cliente excluído com sucesso' : 'Cliente não encontrado'
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
            $cliente = $this->model->buscarPorId($id);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado');
            }

            return [
                'success' => true,
                'data' => $cliente
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
