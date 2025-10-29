<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBeauty - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>MyBeauty</h1>
        <p>Sistema de Gerenciamento de Salão</p>
    </header>

    <main>
        <div class="login-container">
            <div class="login-card">
                <h2>Login</h2>
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    <button type="submit" class="btn-login">Entrar</button>
                </form>
                <p class="forgot-password">
                    <a href="recuperar-senha.php">Esqueceu sua senha?</a>
                </p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 MyBeauty - Todos os direitos reservados</p>
    </footer>
</body>
</html>