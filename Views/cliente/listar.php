<?php require_once __DIR__ . '/../../includes/db_include.php'; ?><?php require_once __DIR__ . '/../../includes/db_include.php'; ?>

<!DOCTYPE html><!DOCTYPE html>

<html lang="pt-br"><html lang="pt-br">

<head><head>

    <meta charset="UTF-8">    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar Clientes</title>    <title>Gerenciar Clientes</title>

    <link rel="stylesheet" href="/style.css">    <link rel="stylesheet" href="/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head></head>

<body><body>

    <div class="container">    <div class="container">

        <h1>Gerenciar Clientes</h1>        <h1>Gerenciar Clientes</h1>

                

        <!-- Filtros -->        <!-- Filtros -->

        <div class="filters">        <div class="filters">

            <input type="text" id="filtroNome" placeholder="Filtrar por nome">            <input type="text" id="filtroNome" placeholder="Filtrar por nome">

            <input type="text" id="filtroEmail" placeholder="Filtrar por email">            <input type="text" id="filtroEmail" placeholder="Filtrar por email">

            <input type="text" id="filtroTelefone" placeholder="Filtrar por telefone">            <input type="text" id="filtroCPF" placeholder="Filtrar por CPF">

            <button onclick="filtrarClientes()">Filtrar</button>            <button onclick="filtrarClientes()">Filtrar</button>

            <button onclick="mostrarModalCliente()" class="btn-primary">Novo Cliente</button>            <button onclick="mostrarModalCliente()" class="btn-primary">Novo Cliente</button>

        </div>        </div>



        <!-- Tabela de Clientes -->        <!-- Tabela de Clientes -->

        <div class="table-responsive">        <div class="table-responsive">

            <table id="tabelaClientes">            <table id="tabelaClientes">

                <thead>                <thead>

                    <tr>                    <tr>

                        <th>Nome</th>                        <th>Nome</th>

                        <th>Email</th>                        <th>Email</th>

                        <th>Telefone</th>                        <th>Telefone</th>

                        <th>Ações</th>                        <th>CPF</th>

                    </tr>                        <th>Ações</th>

                </thead>                    </tr>

                <tbody>                </thead>

                    <!-- Preenchido via JavaScript -->                <tbody>

                </tbody>                    <!-- Preenchido via JavaScript -->

            </table>                </tbody>

        </div>            </table>

    </div>        </div>

    </div>

    <!-- Modal de Cliente -->

    <div id="modalCliente" class="modal">    <!-- Modal de Cliente -->

        <div class="modal-content">    <div id="modalCliente" class="modal">

            <span class="close">&times;</span>        <div class="modal-content">

            <h2>Cliente</h2>            <span class="close">&times;</span>

            <form id="formCliente">            <h2>Cliente</h2>

                <input type="hidden" id="clienteId">            <form id="formCliente">

                <div class="form-group">                <input type="hidden" id="clienteId">

                    <label for="nome">Nome:</label>                <div class="form-group">

                    <input type="text" id="nome" required>                    <label for="nome">Nome:</label>

                </div>                    <input type="text" id="nome" required>

                <div class="form-group">                </div>

                    <label for="email">Email:</label>                <div class="form-group">

                    <input type="email" id="email" required>                    <label for="email">Email:</label>

                </div>                    <input type="email" id="email" required>

                <div class="form-group">                </div>

                    <label for="telefone">Telefone:</label>                <div class="form-group">

                    <input type="tel" id="telefone" required>                    <label for="telefone">Telefone:</label>

                </div>                    <input type="tel" id="telefone" required>

                <button type="submit" class="btn-primary">Salvar</button>                </div>

            </form>                <div class="form-group">

        </div>                    <label for="cpf">CPF:</label>

    </div>                    <input type="text" id="cpf" required>

                </div>

    <script>                <button type="submit" class="btn-primary">Salvar</button>

        // Funções auxiliares            </form>

        function formatarTelefone(telefone) {        </div>

            telefone = telefone.replace(/\D/g, '');    </div>

            if (telefone.length === 11) {

                return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');    <script>

            }        // Funções auxiliares

            return telefone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');        function formatarCPF(cpf) {

        }            cpf = cpf.replace(/\D/g, '');

            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');

        function mostrarModalCliente(id = null) {        }

            const modal = document.getElementById('modalCliente');

            modal.style.display = 'block';        function formatarTelefone(telefone) {

                        telefone = telefone.replace(/\D/g, '');

            if (id) {            if (telefone.length === 11) {

                // Carregar dados do cliente para edição                return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');

                fetch(`/api/clientes.php/${id}`)            }

                    .then(response => response.json())            return telefone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');

                    .then(data => {        }

                        if (data.success) {

                            const cliente = data.data;        function mostrarModalCliente(id = null) {

                            document.getElementById('clienteId').value = cliente.id;            const modal = document.getElementById('modalCliente');

                            document.getElementById('nome').value = cliente.nome;            modal.style.display = 'block';

                            document.getElementById('email').value = cliente.email;            

                            document.getElementById('telefone').value = cliente.telefone;            if (id) {

                        }                // Carregar dados do cliente para edição

                    });                fetch(`/api/clientes.php/${id}`)

            } else {                    .then(response => response.json())

                // Novo cliente                    .then(data => {

                document.getElementById('formCliente').reset();                        if (data.success) {

                document.getElementById('clienteId').value = '';                            const cliente = data.data;

            }                            document.getElementById('clienteId').value = cliente.id;

        }                            document.getElementById('nome').value = cliente.nome;

                            document.getElementById('email').value = cliente.email;

        // Fechar modal                            document.getElementById('telefone').value = cliente.telefone;

        document.querySelector('.close').onclick = function() {                            document.getElementById('cpf').value = formatarCPF(cliente.cpf);

            document.getElementById('modalCliente').style.display = 'none';                        }

        }                    });

            } else {

        // Carregar clientes                // Novo cliente

        function carregarClientes(filtros = {}) {                document.getElementById('formCliente').reset();

            let url = '/api/clientes.php';                document.getElementById('clienteId').value = '';

            if (Object.keys(filtros).length > 0) {            }

                url += '?' + new URLSearchParams(filtros);        }

            }

        // Fechar modal

            fetch(url)        document.querySelector('.close').onclick = function() {

                .then(response => response.json())            document.getElementById('modalCliente').style.display = 'none';

                .then(data => {        }

                    if (data.success) {

                        const tbody = document.querySelector('#tabelaClientes tbody');        // Carregar clientes

                        tbody.innerHTML = '';        function carregarClientes(filtros = {}) {

            let url = '/api/clientes.php';

                        data.data.forEach(cliente => {            if (Object.keys(filtros).length > 0) {

                            const tr = document.createElement('tr');                url += '?' + new URLSearchParams(filtros);

                            tr.innerHTML = `            }

                                <td>${cliente.nome}</td>

                                <td>${cliente.email}</td>            fetch(url)

                                <td>${formatarTelefone(cliente.telefone)}</td>                .then(response => response.json())

                                <td>                .then(data => {

                                    <button onclick="mostrarModalCliente(${cliente.id})" class="btn-edit">                    if (data.success) {

                                        <i class="fas fa-edit"></i>                        const tbody = document.querySelector('#tabelaClientes tbody');

                                    </button>                        tbody.innerHTML = '';

                                    <button onclick="excluirCliente(${cliente.id})" class="btn-delete">

                                        <i class="fas fa-trash"></i>                        data.data.forEach(cliente => {

                                    </button>                            const tr = document.createElement('tr');

                                </td>                            tr.innerHTML = `

                            `;                                <td>${cliente.nome}</td>

                            tbody.appendChild(tr);                                <td>${cliente.email}</td>

                        });                                <td>${formatarTelefone(cliente.telefone)}</td>

                    }                                <td>${formatarCPF(cliente.cpf)}</td>

                });                                <td>

        }                                    <button onclick="mostrarModalCliente(${cliente.id})" class="btn-edit">

                                        <i class="fas fa-edit"></i>

        // Salvar cliente                                    </button>

        document.getElementById('formCliente').onsubmit = function(e) {                                    <button onclick="excluirCliente(${cliente.id})" class="btn-delete">

            e.preventDefault();                                        <i class="fas fa-trash"></i>

                                    </button>

            const id = document.getElementById('clienteId').value;                                </td>

            const dados = {                            `;

                nome: document.getElementById('nome').value,                            tbody.appendChild(tr);

                email: document.getElementById('email').value,                        });

                telefone: document.getElementById('telefone').value.replace(/\D/g, '')                    }

            };                });

        }

            const method = id ? 'PUT' : 'POST';

            const url = id ? `/api/clientes.php/${id}` : '/api/clientes.php';        // Salvar cliente

        document.getElementById('formCliente').onsubmit = function(e) {

            fetch(url, {            e.preventDefault();

                method: method,

                headers: {            const id = document.getElementById('clienteId').value;

                    'Content-Type': 'application/json'            const dados = {

                },                nome: document.getElementById('nome').value,

                body: JSON.stringify(dados)                email: document.getElementById('email').value,

            })                telefone: document.getElementById('telefone').value.replace(/\D/g, ''),

            .then(response => response.json())                cpf: document.getElementById('cpf').value.replace(/\D/g, '')

            .then(data => {            };

                if (data.success) {

                    alert(data.message);            const method = id ? 'PUT' : 'POST';

                    document.getElementById('modalCliente').style.display = 'none';            const url = id ? `/api/clientes.php/${id}` : '/api/clientes.php';

                    carregarClientes();

                } else {            fetch(url, {

                    alert(data.error);                method: method,

                }                headers: {

            });                    'Content-Type': 'application/json'

        };                },

                body: JSON.stringify(dados)

        // Excluir cliente            })

        function excluirCliente(id) {            .then(response => response.json())

            if (confirm('Tem certeza que deseja excluir este cliente? Todos os seus dados serão removidos.')) {            .then(data => {

                fetch(`/api/clientes.php/${id}`, {                if (data.success) {

                    method: 'DELETE'                    alert(data.message);

                })                    document.getElementById('modalCliente').style.display = 'none';

                .then(response => response.json())                    carregarClientes();

                .then(data => {                } else {

                    if (data.success) {                    alert(data.error);

                        alert(data.message);                }

                        carregarClientes();            });

                    } else {        };

                        alert(data.error);

                    }        // Excluir cliente

                });        function excluirCliente(id) {

            }            if (confirm('Tem certeza que deseja excluir este cliente?')) {

        }                fetch(`/api/clientes.php/${id}`, {

                    method: 'DELETE'

        // Filtrar clientes                })

        function filtrarClientes() {                .then(response => response.json())

            const filtros = {                .then(data => {

                nome: document.getElementById('filtroNome').value,                    if (data.success) {

                email: document.getElementById('filtroEmail').value,                        alert(data.message);

                telefone: document.getElementById('filtroTelefone').value.replace(/\D/g, '')                        carregarClientes();

            };                    } else {

            carregarClientes(filtros);                        alert(data.error);

        }                    }

                });

        // Máscara para telefone            }

        document.getElementById('telefone').addEventListener('input', function(e) {        }

            let value = e.target.value.replace(/\D/g, '');

            if (value.length <= 11) {        // Filtrar clientes

                e.target.value = formatarTelefone(value);        function filtrarClientes() {

            }            const filtros = {

        });                nome: document.getElementById('filtroNome').value,

                email: document.getElementById('filtroEmail').value,

        // Inicialização                cpf: document.getElementById('filtroCPF').value.replace(/\D/g, '')

        document.addEventListener('DOMContentLoaded', function() {            };

            carregarClientes();            carregarClientes(filtros);

        });        }

    </script>

</body>        // Máscaras para inputs

</html>        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                e.target.value = formatarCPF(value);
            }
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                e.target.value = formatarTelefone(value);
            }
        });

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            carregarClientes();
        });
    </script>
</body>
</html>
