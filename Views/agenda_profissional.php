<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Agenda - My Beauty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Minha Agenda</h1>
        <nav>
            <a href="index.php?acao=inicio">Início</a> |
            <a href="index.php?acao=logout">Sair</a>
        </nav>
    </header>

    <section>
        <h2>Meus Próximos Atendimentos</h2>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Serviços</th>
                    <th>Data e Hora</th>
                    <th>Status</th>
                    <th>Ação</th> 
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Esta variável $lista_agenda_profissional será carregada pelo Controller
                    if (isset($lista_agenda_profissional) && !empty($lista_agenda_profissional)):
                        foreach($lista_agenda_profissional as $a): 
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->cliente_nome ?></td>
                    <td><?= $a->servicos ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($a->data_hora)) ?></td>
                    <td><?= $a->status ?></td>
                    <td>
                        <a href="#">Confirmar</a> | 
                        <a href="#">Cancelar</a>
                    </td>
                </tr>
                <?php 
                        endforeach;
                    else:
                ?>
                <tr>
                    <td colspan="6">Nenhum agendamento encontrado para você.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</body>
</html>