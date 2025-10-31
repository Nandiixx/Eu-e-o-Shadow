<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>My Beauty - Painel Profissional</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Painel do Profissional, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
        <nav>
            <a href="index.php?acao=inicio">Início</a>
            <a href="index.php?acao=agenda_profissional_mostrar">Minha Agenda</a>
            <a href="index.php?acao=logout">Sair</a>
        </nav>
    </header>
    <main>
        <div class="welcome-message">
            Bem-vindo(a) ao seu painel de controle!
        </div>
        <div class="professional-panel">
            <h2>Seus Próximos Atendimentos</h2>
            <p>Use o menu acima para navegar pelo sistema.</p>
            <p>Clique em "Minha Agenda" para visualizar e gerenciar seus compromissos.</p>
        </div>
    </main>
</body>
</html>