<?php require_once __DIR__ . '/../../includes/db_include.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Profissionais</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Profissionais</h1>

        <div class="filters">
            <input type="text" id="filtroNome" placeholder="Filtrar por nome">
            <select id="filtroCargo">
                <option value="">Todos os cargos</option>
                <option value="PROFISSIONAL_BELEZA">Profissional de Beleza</option>
                <option value="RECEPCIONISTA">Recepcionista</option>
                <option value="PROPRIETARIO">Proprietário</option>
                <option value="GERENTE_FINANCEIRO">Gerente Financeiro</option>
            </select>
            <button onclick="filtrarProfissionais()">Filtrar</button>
            <button onclick="mostrarModalProfissional()" class="btn-primary">Novo Profissional</button>
        </div>

        <div class="table-responsive">
            <table id="tabelaProfissionais">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Matrícula</th>
                        <th>Cargo</th>
                        <th>Especialidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalProfissional" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Profissional</h2>
            <form id="formProfissional">
                <input type="hidden" id="profissionalId">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" required>
                </div>
                <div class="form-group">
                    <label for="matricula">Matrícula:</label>
                    <input type="text" id="matricula" required>
                </div>
                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <select id="cargo" required>
                        <option value="PROFISSIONAL_BELEZA">Profissional de Beleza</option>
                        <option value="RECEPCIONISTA">Recepcionista</option>
                        <option value="PROPRIETARIO">Proprietário</option>
                        <option value="GERENTE_FINANCEIRO">Gerente Financeiro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="especialidade">Especialidade:</label>
                    <input type="text" id="especialidade">
                </div>

                <button type="submit" class="btn-primary">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        function mostrarModalProfissional(id = null) {
            const modal = document.getElementById('modalProfissional');
            modal.style.display = 'block';

            if (id) {
                fetch(`/api/profissionais.php/${id}`)
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            const p = res.data;
                            document.getElementById('profissionalId').value = p.id;
                            document.getElementById('nome').value = p.nome;
                            document.getElementById('matricula').value = p.matricula || '';
                            document.getElementById('cargo').value = p.cargo;
                            document.getElementById('especialidade').value = p.especialidade || '';
                        } else {
                            alert(res.error || 'Erro ao carregar profissional');
                        }
                    });
            } else {
                document.getElementById('formProfissional').reset();
                document.getElementById('profissionalId').value = '';
            }
        }

        document.querySelectorAll('.close').forEach(el => el.onclick = function(){
            document.getElementById('modalProfissional').style.display = 'none';
        });

        function carregarProfissionais(filtros = {}) {
            let url = '/api/profissionais.php';
            if (Object.keys(filtros).length) url += '?' + new URLSearchParams(filtros);

            fetch(url)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const tbody = document.querySelector('#tabelaProfissionais tbody');
                        tbody.innerHTML = '';
                        res.data.forEach(p => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${p.nome}</td>
                                <td>${p.matricula || ''}</td>
                                <td>${p.cargo}</td>
                                <td>${p.especialidade || ''}</td>
                                <td>
                                    <button onclick="mostrarModalProfissional(${p.id})" class="btn-edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="excluirProfissional(${p.id})" class="btn-delete"><i class="fas fa-trash"></i></button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        alert(res.error || 'Erro ao listar profissionais');
                    }
                });
        }

        document.getElementById('formProfissional').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('profissionalId').value;
            const dados = {
                nome: document.getElementById('nome').value,
                matricula: document.getElementById('matricula').value,
                cargo: document.getElementById('cargo').value,
                especialidade: document.getElementById('especialidade').value
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/profissionais.php/${id}` : '/api/profissionais.php';

            fetch(url, {
                method,
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(dados)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert(res.message || 'Operação realizada');
                    document.getElementById('modalProfissional').style.display = 'none';
                    carregarProfissionais();
                } else {
                    alert(res.error || 'Erro ao salvar');
                }
            });
        };

        function excluirProfissional(id) {
            if (!confirm('Excluir profissional?')) return;
            fetch(`/api/profissionais.php/${id}`, { method: 'DELETE' })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        alert(res.message || 'Excluído');
                        carregarProfissionais();
                    } else alert(res.error || 'Erro ao excluir');
                });
        }

        function filtrarProfissionais() {
            const filtros = {
                nome: document.getElementById('filtroNome').value,
                cargo: document.getElementById('filtroCargo').value
            };
            carregarProfissionais(filtros);
        }

        document.addEventListener('DOMContentLoaded', function(){
            carregarProfissionais();
        });
    </script>
</body>
</html>
