<?php
require_once '../Models/Cliente.php';

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erro = null;
    
    // Validações
    if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha']) || 
        empty($_POST['confirma_senha']) || empty($_POST['telefone'])) {
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
        $cliente = new Cliente();
        $cliente->setNome($_POST['nome']);
        $cliente->setEmail($_POST['email']);
        $cliente->setSenha($_POST['senha']);
        $cliente->setTelefone($_POST['telefone']);

        if ($cliente->inserirBD()) {
            // Redireciona em caso de sucesso
            header("Location: login.php?cadastro=sucesso");
            exit;
        } else {
            $erro = "Erro ao cadastrar. Por favor, tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBeauty - Cadastro de Cliente</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <main class="auth-bg">
        <div class="auth-pattern"></div>
        <section class="auth-wrapper register-page">
            <div class="auth-card">
                <div class="auth-brand">
                    <div class="brand-logo" aria-hidden="true">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 3c2.8 0 5 2.2 5 5 0 1.6-.8 3.1-2 4 2.8.4 5 2.8 5 5.7V19c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2v-1.3c0-2.9 2.2-5.3 5-5.7-1.2-.9-2-2.4-2-4 0-2.8 2.2-5 5-5Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="brand-title">Criar Conta - MyBeauty</h1>
                        <p class="brand-subtitle">Cadastre-se como cliente</p>
                    </div>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="alert-error" role="alert"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <form class="auth-form" method="POST" action="" novalidate>
                    <div class="form-grid-2">
                        <div class="input-field">
                            <span class="input-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <input type="text" id="nome" name="nome" placeholder="Nome completo" required 
                                   value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                        </div>

                        <div class="input-field">
                            <span class="input-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/>
                                </svg>
                            </span>
                            <input type="email" id="email" name="email" placeholder="E-mail" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="input-field">
                            <span class="input-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 8h-1V6a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <input type="password" id="senha" name="senha" placeholder="Senha" required minlength="6">
                        </div>

                        <div class="input-field">
                            <span class="input-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 8h-1V6a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirmar senha" required minlength="6">
                        </div>

                        <div class="input-field">
                            <span class="input-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 15.5c-1.2 0-2.4-.2-3.6-.6-.3-.1-.7 0-1 .2l-2.2 2.2c-2.8-1.4-5.1-3.8-6.5-6.5l2.2-2.2c.3-.3.4-.7.2-1-.3-1.1-.5-2.3-.5-3.5 0-.6-.4-1-1-1H4c-.6 0-1 .4-1 1 0 9.4 7.6 17 17 17 .6 0 1-.4 1-1v-3.5c0-.6-.4-1-1-1zM19 12h2a9 9 0 0 0-9-9v2c3.9 0 7 3.1 7 7zm-4 0h2c0-2.8-2.2-5-5-5v2c1.7 0 3 1.3 3 3z" fill="currentColor"/>
                                </svg>
                            </span>
                            <input type="tel" id="telefone" name="telefone" placeholder="Telefone" required
                                   value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">
                        </div>

                        <button type="submit" class="btn-primary" data-loading="false" aria-busy="false">
                            <span class="btn-label">Criar conta</span>
                            <span class="btn-spinner" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>

                <div class="auth-divider" role="separator" aria-label="ou"></div>

                <div class="auth-cta">
                    <p class="auth-cta-text">Já tem uma conta? <a class="auth-cta-link" href="../index.php">Entrar</a></p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
