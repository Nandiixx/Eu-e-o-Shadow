<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>My Beauty - Painel Profissional</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Painel do Profissional, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
        <nav>
            <a href="index.php?acao=inicio">Início</a> |
            <a href="index.php?acao=agenda_profissional_mostrar">Minha Agenda</a> |
            <a href="index.php?acao=logout">Sair</a>
        </nav>
    </header>
    <main>
        <h2>Seus Próximos Atendimentos</h2>
        <p>Bem-vindo ao seu painel. Use o menu acima para navegar.</p>
        <p>Clique em "Minha Agenda" para ver seus compromissos.</p>
        </main>
</body>
</html>