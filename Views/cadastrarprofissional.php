<?php
require_once '../Models/Funcionario.php';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erro = null;
    
    // Validações
    if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha']) || 
        empty($_POST['confirma_senha']) || empty($_POST['matricula']) || empty($_POST['cargo'])) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos.";
    }
    else if ($_POST['senha'] !== $_POST['confirma_senha']) {
        $erro = "As senhas não coincidem.";
    }
    else if (strlen($_POST['senha']) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    }
    else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    }
    else {
        // Tenta cadastrar
        $funcionario = new Funcionario();
        $funcionario->setNome($_POST['nome']);
        $funcionario->setEmail($_POST['email']);
        $funcionario->setSenha($_POST['senha']);
        $funcionario->setCargo($_POST['cargo']);

        if ($funcionario->inserirBD()) {
            // Redireciona em caso de sucesso
            header("Location: listar.php?sucesso=1");
            exit;
        } else {
            $erro = "Erro ao cadastrar profissional. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Profissional</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Cadastrar Profissional</h1>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" required 
                       value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required minlength="6">
            </div>

            <div class="form-group">
                <label for="confirma_senha">Confirmar Senha</label>
                <input type="password" name="confirma_senha" id="confirma_senha" required minlength="6">
            </div>

            <div class="form-group">
                <label for="matricula">Matrícula</label>
                <input type="text" name="matricula" id="matricula" required
                       value="<?php echo isset($_POST['matricula']) ? htmlspecialchars($_POST['matricula']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="cargo">Cargo</label>
                <select name="cargo" id="cargo" required>
                    <option value="">Selecione um cargo</option>
                    <option value="PROFISSIONAL_BELEZA" <?php echo (isset($_POST['cargo']) && $_POST['cargo'] === 'PROFISSIONAL_BELEZA') ? 'selected' : ''; ?>>
                        Profissional de Beleza
                    </option>
                    <option value="RECEPCIONISTA" <?php echo (isset($_POST['cargo']) && $_POST['cargo'] === 'RECEPCIONISTA') ? 'selected' : ''; ?>>
                        Recepcionista
                    </option>
                    <option value="PROPRIETARIO" <?php echo (isset($_POST['cargo']) && $_POST['cargo'] === 'PROPRIETARIO') ? 'selected' : ''; ?>>
                        Proprietário
                    </option>
                    <option value="GERENTE_FINANCEIRO" <?php echo (isset($_POST['cargo']) && $_POST['cargo'] === 'GERENTE_FINANCEIRO') ? 'selected' : ''; ?>>
                        Gerente Financeiro
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="especialidade">Especialidade</label>
                <input type="text" name="especialidade" id="especialidade"
                       value="<?php echo isset($_POST['especialidade']) ? htmlspecialchars($_POST['especialidade']) : ''; ?>">
                <small class="form-text text-muted">Opcional. Preencha apenas se for Profissional de Beleza.</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primary">Cadastrar</button>
                <a href="listar.php" class="btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html>