<?php require_once __DIR__ . '/../../includes/db_include.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Agendamentos</title>
    <link rel="stylesheet" href="/style.css">
    <!-- Adiciona Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciar Agendamentos</h1>
        
        <!-- Filtros -->
        <div class="filters">
            <input type="date" id="filtroData" placeholder="Filtrar por data">
            <select id="filtroProfissional">
                <option value="">Todos os profissionais</option>
            </select>
            <button onclick="filtrarAgendamentos()">Filtrar</button>
            <button onclick="mostrarModalAgendamento()" class="btn-primary">Novo Agendamento</button>
        </div>

        <!-- Tabela de Agendamentos -->
        <div class="table-responsive">
            <table id="tabelaAgendamentos">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Cliente</th>
                        <th>Profissional</th>
                        <th>Serviço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Preenchido via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Agendamento -->
    <div id="modalAgendamento" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Agendamento</h2>
            <form id="formAgendamento">
                <input type="hidden" id="agendamentoId">
                <div class="form-group">
                    <label for="cliente">Cliente:</label>
                    <select id="cliente" required>
                        <option value="">Selecione o cliente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="profissional">Profissional:</label>
                    <select id="profissional" required>
                        <option value="">Selecione o profissional</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="servico">Serviço:</label>
                    <select id="servico" required>
                        <option value="">Selecione o serviço</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dataHora">Data e Hora:</label>
                    <input type="datetime-local" id="dataHora" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" required>
                        <option value="pendente">Pendente</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="concluido">Concluído</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        // Funções auxiliares
        function formatarData(data) {
            return new Date(data).toLocaleString('pt-BR');
        }

        function mostrarModalAgendamento(id = null) {
            const modal = document.getElementById('modalAgendamento');
            modal.style.display = 'block';
            
            if (id) {
                // Carregar dados do agendamento para edição
                fetch(`/api/agendamentos.php/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const agendamento = data.data;
                            document.getElementById('agendamentoId').value = agendamento.id;
                            document.getElementById('cliente').value = agendamento.cliente_id;
                            document.getElementById('profissional').value = agendamento.profissional_id;
                            document.getElementById('servico').value = agendamento.servico_id;
                            document.getElementById('dataHora').value = agendamento.data_hora.replace(' ', 'T');
                            document.getElementById('status').value = agendamento.status;
                        }
                    });
            } else {
                // Novo agendamento
                document.getElementById('formAgendamento').reset();
                document.getElementById('agendamentoId').value = '';
            }
        }

        // Fechar modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('modalAgendamento').style.display = 'none';
        }

        // Carregar agendamentos
        function carregarAgendamentos(filtros = {}) {
            let url = '/api/agendamentos.php';
            if (Object.keys(filtros).length > 0) {
                url += '?' + new URLSearchParams(filtros);
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.querySelector('#tabelaAgendamentos tbody');
                        tbody.innerHTML = '';

                        data.data.forEach(agendamento => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${formatarData(agendamento.data_hora)}</td>
                                <td>${agendamento.cliente_nome}</td>
                                <td>${agendamento.profissional_nome}</td>
                                <td>${agendamento.servico_nome}</td>
                                <td>${agendamento.status}</td>
                                <td>
                                    <button onclick="mostrarModalAgendamento(${agendamento.id})" class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="excluirAgendamento(${agendamento.id})" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                });
        }

        // Carregar dados para os selects
        function carregarDadosSelects() {
            // Carregar clientes
            fetch('/api/clientes.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('cliente');
                        data.data.forEach(cliente => {
                            const option = document.createElement('option');
                            option.value = cliente.id;
                            option.textContent = cliente.nome;
                            select.appendChild(option);
                        });
                    }
                });

            // Carregar profissionais
            fetch('/api/profissionais.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('profissional');
                        const filtroSelect = document.getElementById('filtroProfissional');
                        data.data.forEach(profissional => {
                            const option = document.createElement('option');
                            const filtroOption = option.cloneNode(true);
                            option.value = profissional.id;
                            option.textContent = profissional.nome;
                            filtroOption.value = profissional.id;
                            filtroOption.textContent = profissional.nome;
                            select.appendChild(option);
                            filtroSelect.appendChild(filtroOption);
                        });
                    }
                });

            // Carregar serviços
            fetch('/api/servicos.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('servico');
                        data.data.forEach(servico => {
                            const option = document.createElement('option');
                            option.value = servico.id;
                            option.textContent = servico.nome;
                            select.appendChild(option);
                        });
                    }
                });
        }

        // Salvar agendamento
        document.getElementById('formAgendamento').onsubmit = function(e) {
            e.preventDefault();

            const id = document.getElementById('agendamentoId').value;
            const dados = {
                cliente_id: document.getElementById('cliente').value,
                profissional_id: document.getElementById('profissional').value,
                servico_id: document.getElementById('servico').value,
                data_hora: document.getElementById('dataHora').value.replace('T', ' '),
                status: document.getElementById('status').value
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/agendamentos.php/${id}` : '/api/agendamentos.php';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.getElementById('modalAgendamento').style.display = 'none';
                    carregarAgendamentos();
                } else {
                    alert(data.error);
                }
            });
        };

        // Excluir agendamento
        function excluirAgendamento(id) {
            if (confirm('Tem certeza que deseja excluir este agendamento?')) {
                fetch(`/api/agendamentos.php/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        carregarAgendamentos();
                    } else {
                        alert(data.error);
                    }
                });
            }
        }

        // Filtrar agendamentos
        function filtrarAgendamentos() {
            const filtros = {
                data: document.getElementById('filtroData').value,
                profissional_id: document.getElementById('filtroProfissional').value
            };
            carregarAgendamentos(filtros);
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            carregarDadosSelects();
            carregarAgendamentos();
        });
    </script>
</body>
</html>
