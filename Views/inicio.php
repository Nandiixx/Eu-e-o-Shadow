<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>My Beauty - Início</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bem-vindo ao My Beauty, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
        <nav>
            <a href="index.php?acao=inicio">Início</a> |
            <a href="index.php?acao=agendamento_mostrar">Agendamentos</a> |
            <a href="index.php?acao=logout">Sair</a>
        </nav>
    </header>
    <main>
        <h2>Painel do Sistema</h2>
        <p>Escolha uma opção no menu acima para continuar.</p>
    </main>
</body>
</html>