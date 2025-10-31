<?php require_once __DIR__ . '/../../includes/db_include.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Profissional</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="container">
        <h1>Cadastrar Profissional</h1>
        <form id="formCadastrar">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" required>
            </div>
            <div class="form-group">
                <label for="matricula">Matrícula</label>
                <input type="text" id="matricula" required>
            </div>
            <div class="form-group">
                <label for="cargo">Cargo</label>
                <select id="cargo" required>
                    <option value="PROFISSIONAL_BELEZA">Profissional de Beleza</option>
                    <option value="RECEPCIONISTA">Recepcionista</option>
                    <option value="PROPRIETARIO">Proprietário</option>
                    <option value="GERENTE_FINANCEIRO">Gerente Financeiro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="especialidade">Especialidade</label>
                <input type="text" id="especialidade">
            </div>
            <button type="submit" class="btn-primary">Salvar</button>
        </form>
    </div>

    <script>
        document.getElementById('formCadastrar').onsubmit = function(e){
            e.preventDefault();
            const dados = {
                nome: document.getElementById('nome').value,
                matricula: document.getElementById('matricula').value,
                cargo: document.getElementById('cargo').value,
                especialidade: document.getElementById('especialidade').value
            };

            fetch('/api/profissionais.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(dados)
            })
            .then(r=>r.json())
            .then(res=>{
                if (res.success) {
                    alert(res.message || 'Profissional cadastrado');
                    window.location.href = '/Views/profissional/listar.php';
                } else {
                    alert(res.error || 'Erro ao cadastrar');
                }
            });
        };
    </script>
</body>
</html>
