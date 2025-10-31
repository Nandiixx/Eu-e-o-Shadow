<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos - My Beauty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Gerenciamento de Agendamentos</h1>
        <nav>
            <a href="index.php?acao=inicio">Início</a> |
            <a href="index.php?acao=logout">Sair</a>
        </nav>
    </header>

    <section>
        ...
        <h2>Novo Agendamento</h2>
        <form action="index.php" method="POST">
            <input type="hidden" name="acao" value="agendamento_salvar">

            <label>Profissional:</label><br>
            <select name="profissional_id" required>
                <option value="">Selecione um profissional</option>
                <?php 
                    // Lista carregada pelo AgendamentoController::index()
                    // vinda do Model/Funcionario.php
                    if (isset($lista_profissionais) && !empty($lista_profissionais)) {
                        foreach($lista_profissionais as $prof):
                ?>
                    <option value="<?= $prof->id; ?>">
                        <?= $prof->nome; ?>
                    </option>
                <?php 
                        endforeach;
                    } else {
                        echo "<option value=''>Nenhum profissional disponível</option>";
                    }
                ?>
            </select><br><br>

            <label>Serviço:</label><br>
...            <select name="servico_id" required>
                <option value="">Selecione um serviço</option>
                <?php 
                    // Lista carregada pelo AgendamentoController::index()
                    if (isset($lista_servicos) && !empty($lista_servicos)) {
                        foreach($lista_servicos as $s):
                ?>
                    <option value="<?= $s->id; ?>">
                        <?= $s->nome; ?> - R$ <?= number_format($s->preco, 2, ',', '.'); ?>
                    </option>
                <?php 
                        endforeach;
                    } else {
                        echo "<option value=''>Nenhum serviço disponível</option>";
                    }
                ?>

            <label>Data e Hora:</label><br>
            <input type="datetime-local" name="dataHora" required><br><br>

            <button type="submit">Agendar</button>
        </form>
    </section>

    <section>
        <h2>Meus Agendamentos</h2>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profissional</th>
                    <th>Serviços</th>
                    <th>Data e Hora</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Lista carregada pelo AgendamentoController
                    if (isset($lista_agendamentos) && !empty($lista_agendamentos)):
                        foreach($lista_agendamentos as $a): 
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->profissional_nome ?></td>
                    <td><?= $a->servicos ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($a->data_hora)) ?></td>
                    <td><?= $a->status ?></td>
                </tr>
                <?php 
                        endforeach;
                    else:
                ?>
                <tr>
                    <td colspan="5">Nenhum agendamento encontrado.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</body>
</html>