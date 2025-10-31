<?php
// Inicia a sessão se já não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// As variáveis $lista_profissionais, $lista_servicos e $lista_agendamentos
// são injetadas pelo AgendamentoController::index()
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos - My Beauty</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Gerenciamento de Agendamentos</h1>
        <nav>
            <a href="../index.php?acao=inicio">Início</a> |
            <a href="../index.php?acao=logout">Sair</a>
        </nav>
    </header>

    <section>
        <h2>Novo Agendamento</h2>

        <?php
            if (isset($_SESSION['erros_agendamento'])) {
                echo '<div class="alert-error" role="alert">';
                foreach ($_SESSION['erros_agendamento'] as $erro) {
                    echo "<p>" . htmlspecialchars($erro) . "</p>";
                }
                echo '</div>';
                unset($_SESSION['erros_agendamento']); // Limpa após exibir
            }

            if (isset($_SESSION['sucesso_agendamento'])) {
                echo '<div class="alert-success" role="alert">';
                echo "<p>" . htmlspecialchars($_SESSION['sucesso_agendamento']) . "</p>";
                echo '</div>';
                unset($_SESSION['sucesso_agendamento']); // Limpa após exibir
            }
        ?>
        <form action="../index.php" method="POST">
            <input type="hidden" name="acao" value="agendamento_salvar">

            <label>Profissional:</label><br>
            <select name="profissional_id" required>
                <option value="">Selecione um profissional</option>
                <?php 
                    if (isset($lista_profissionais) && !empty($lista_profissionais)) {
                        foreach($lista_profissionais as $prof):
                ?>
                    <option value="<?= htmlspecialchars($prof->id); ?>">
                        <?= htmlspecialchars($prof->nome); ?>
                    </option>
                <?php 
                        endforeach;
                    } else {
                        echo "<option value=''>Nenhum profissional disponível</option>";
                    }
                ?>
            </select><br><br>

            <label>Serviço(s): (Segure Ctrl/Cmd para selecionar mais de um)</label><br>
            <select name="servicos_ids[]" required multiple size="5">
                <?php 
                    if (isset($lista_servicos) && !empty($lista_servicos)) {
                        foreach($lista_servicos as $s):
                ?>
                    <option value="<?= htmlspecialchars($s->id); ?>">
                        <?= htmlspecialchars($s->nome); ?> - R$ <?= number_format($s->preco, 2, ',', '.'); ?>
                    </option>
                <?php 
                        endforeach;
                    } else {
                        echo "<option value=''>Nenhum serviço disponível</option>";
                    }
                ?>
            </select><br><br>

            <label>Data e Hora:</label><br>
            <input type="datetime-local" name="dataHora" required><br><br>

            <button type="submit">Agendar</button>
        </form>
    </section>

    <section>
        <h2>Meus Agendamentos</h2>
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
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
                    if (isset($lista_agendamentos) && !empty($lista_agendamentos)):
                        foreach($lista_agendamentos as $a): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($a->id) ?></td>
                    <td><?= htmlspecialchars($a->profissional_nome) ?></td>
                    <td><?= htmlspecialchars($a->servicos) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($a->data_hora)) ?></td>
                    <td><?= htmlspecialchars($a->status) ?></td>
                </tr>
                <?php 
                        endforeach;
                    else:
                ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Nenhum agendamento encontrado.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</body>
</html>