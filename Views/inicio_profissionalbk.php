<?php
require_once __DIR__ . '/bootstrap.php';

$appointments = [];
try {
    $pdo = Database::getConnection();
    if (!$pdo) throw new Exception('Sem conexão com o banco de dados');

    $sql = "SELECT ag.id, ag.data_hora, ag.status,
                   cu.nome AS cliente_nome,
                   pu.nome AS profissional_nome,
                   GROUP_CONCAT(s.nome SEPARATOR ', ') AS servicos,
                   IFNULL(SUM(s.preco), 0) AS total
            FROM Agendamento ag
            JOIN Cliente c ON ag.cliente_id = c.id
            JOIN Usuario cu ON c.usuario_id = cu.id
            JOIN Funcionario f ON ag.profissional_id = f.id
            JOIN Usuario pu ON f.usuario_id = pu.id
            LEFT JOIN Agendamento_Servicos asgs ON ag.id = asgs.agendamento_id
            LEFT JOIN Servico s ON asgs.servico_id = s.id
            WHERE ag.data_hora >= NOW()
            GROUP BY ag.id
            ORDER BY ag.data_hora ASC
            LIMIT 10";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $appointments = $stmt->fetchAll();

} catch (Exception $e) {
    // Em caso de erro de conexão, fallback para dados de exemplo e log opcional
    error_log('DB connection or query failed: ' . $e->getMessage());
    $appointments = [
        ['cliente_nome'=>'Mariana Silva','servicos'=>'Coloração, Corte','profissional_nome'=>'Bianca','data_hora'=>'2025-10-30 14:00:00','total'=>250.00,'status'=>'AGENDADO'],
        ['cliente_nome'=>'João Souza','servicos'=>'Barba, Corte','profissional_nome'=>'Carlos','data_hora'=>'2025-10-30 15:30:00','total'=>85.00,'status'=>'AGENDADO'],
        ['cliente_nome'=>'Ana Pereira','servicos'=>'Manicure','profissional_nome'=>'Fernanda','data_hora'=>'2025-10-30 16:00:00','total'=>45.00,'status'=>'AGENDADO'],
    ];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MyBeauty - Painel</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <script defer src="script.js"></script>
        <style>
            /* Pequenas estilizações específicas da homepage, usando variáveis já definidas em style.css */
            :root { --panel-bg: rgba(255,255,255,0.85); }
            body { background: linear-gradient(180deg, rgba(250,246,250,0.35), rgba(255,255,255,0.6)) , url('') center/cover no-repeat; }
            .dashboard { display: grid; grid-template-columns: 220px 1fr; gap: 1.5rem; padding: 2rem; min-height: 100vh; }
            .sidebar { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 14px; padding: 1.25rem; width: 100%; }
            .sidebar h3 { color: var(--text); font-size: 1rem; margin-bottom: 1rem; }
            .nav-list { list-style: none; display: flex; flex-direction: column; gap: 0.85rem; }
            .nav-list a { display:flex; align-items:center; gap:0.75rem; padding:0.6rem 0.75rem; border-radius:10px; color:var(--text); text-decoration:none; font-weight:600; }
            .nav-list a.active { background: linear-gradient(135deg,var(--primary),var(--primary-light)); color:#fff; box-shadow: 0 8px 20px rgba(234,99,140,0.15); }
            .topbar { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; }
            .cards { display:flex; gap:1rem; margin-bottom:1.25rem; }
            .card { background:var(--panel-bg); border-radius:12px; padding:1rem 1.25rem; flex:1; box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
            .main-panel { background: rgba(255,255,255,0.7); border-radius:14px; padding:1.25rem; border:1px solid rgba(27,32,33,0.04); }
            .table { width:100%; border-collapse:collapse; margin-top:0.75rem; }
            .table th, .table td { text-align:left; padding:0.75rem 0.6rem; border-bottom:1px solid rgba(27,32,33,0.06); font-size:0.95rem; }
            .status { display:inline-block; padding:0.25rem 0.6rem; border-radius:999px; font-weight:700; font-size:0.8rem; }
            .status.confirmado { background:#e6f9f0; color:#0b8a4b; }
            .search { display:flex; gap:0.75rem; align-items:center; }
            @media (max-width:900px){ .dashboard{grid-template-columns:1fr; padding:1rem;} .sidebar{order:2;} }
            
            /* Estilos do Dropdown */
            .profile-dropdown { position: relative; }
            .dropdown-trigger { cursor: pointer; }
            .dropdown-menu { 
                display: none;
                position: absolute;
                top: calc(100% + 8px);
                right: 0;
                min-width: 220px;
                background: var(--card-bg);
                border: 1px solid var(--card-border);
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                z-index: 1000;
            }
            .dropdown-menu.show { display: block; }
            .dropdown-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.75rem 1rem;
                color: var(--text);
                text-decoration: none;
                transition: background-color 0.2s;
            }
            .dropdown-item:hover {
                background-color: rgba(0,0,0,0.04);
            }
            .dropdown-item.danger {
                color: #dc3545;
            }
            .dropdown-item.danger:hover {
                background-color: rgba(220,53,69,0.08);
            }
        </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
                <div class="brand-logo" aria-hidden="true" style="width:48px;height:48px;border-radius:12px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3c2.8 0 5 2.2 5 5 0 1.6-.8 3.1-2 4 2.8.4 5 2.8 5 5.7V19c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2v-1.3c0-2.9 2.2-5.3 5-5.7-1.2-.9-2-2.4-2-4 0-2.8 2.2-5 5-5Z" fill="currentColor"/></svg>
                </div>
                <div>
                    <div class="brand-title" style="font-size:1.05rem;color:var(--text);">MyBeauty</div>
                    <div class="brand-subtitle" style="font-size:0.75rem;">Sistema de Gestão - Salão</div>
                </div>
            </div>

            <h3>Navegação</h3>
            <ul class="nav-list">
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="Views/agendamento/agendar.php">Agendamentos</a></li>
                <li><a href="Views/cliente/listar.php">Clientes</a></li>
                <li><a href="Views/profissional/listar.php">Profissionais</a></li>
                <li><a href="Views/servico/listar.php">Serviços</a></li>
                <li><a href="#">Financeiro</a></li>
                <li><a href="#">Relatórios</a></li>
                <li><a href="#">Configurações</a></li>
            </ul>
            <div style="margin-top:1.25rem;">
                <a class="btn-secondary" href="Views/cliente/cadastrar.php">+ Novo cliente</a>
            </div>
        </aside>

        <main>
            <div class="topbar">
                <div class="search">
                    <input placeholder="Buscar por cliente, serviço ou profissional..." style="padding:0.8rem 1rem;border-radius:12px;border:1px solid rgba(27,32,33,0.06);width:420px;"> 
                    <button class="btn-primary" style="width:auto;padding:0.6rem 1rem;">Buscar</button>
                </div>
                <div class="profile-dropdown">
                    <div class="dropdown-trigger" style="display:flex;gap:0.75rem;align-items:center;">
                        <div style="text-align:right;margin-right:0.75rem;">
                            <div style="font-weight:700">Olá, Admin</div>
                            <div style="font-size:0.85rem;color:var(--muted)">salon@mybeauty.com</div>
                        </div>
                        <div style="width:44px;height:44px;border-radius:999px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:grid;place-items:center;color:#fff;">A</div>
                    </div>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">
                            <span>Meu Perfil</span>
                        </a>
                        <a href="#" class="dropdown-item">
                            <span>Configurações</span>
                        </a>
                        <hr style="border:none;border-top:1px solid var(--card-border);margin:0.25rem 0;">
                        <a href="logout.php" class="dropdown-item danger">
                            <span>Sair</span>
                        </a>
                    </div>
                </div>
            </div>

            <section class="cards">
                <div class="card">
                    <div style="font-size:0.85rem;color:var(--muted)">Agendamentos de hoje</div>
                    <div style="font-size:1.6rem;font-weight:700;margin-top:0.35rem;">12</div>
                </div>
                <div class="card">
                    <div style="font-size:0.85rem;color:var(--muted)">Clientes cadastrados</div>
                    <div style="font-size:1.6rem;font-weight:700;margin-top:0.35rem;">842</div>
                </div>
                <div class="card">
                    <div style="font-size:0.85rem;color:var(--muted)">Receita (mês)</div>
                    <div style="font-size:1.6rem;font-weight:700;margin-top:0.35rem;color:#1b9b4a;">R$ 12.450,00</div>
                </div>
            </section>

            <div class="main-panel">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <h2 style="margin:0;font-size:1.05rem;">Próximos Agendamentos</h2>
                    <div style="display:flex;gap:0.5rem;align-items:center;"><a class="btn-secondary" href="Views/agendamento/agendar.php">Novo agendamento</a></div>
                </div>

                <table class="table" aria-label="Lista de agendamentos">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Profissional</th>
                            <th>Data / Hora</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $a): 
                                $status = strtoupper($a['status'] ?? 'AGENDADO');
                                $statusClass = '';
                                if ($status === 'CONCLUIDO') $statusClass = 'confirmado';
                                if ($status === 'CANCELADO') $statusClass = '';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($a['cliente_nome'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($a['servicos'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($a['profissional_nome'] ?? '—') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($a['data_hora'] ?? 'now')) ?></td>
                                <td>R$ <?= number_format((float)($a['total'] ?? 0), 2, ',', '.') ?></td>
                                <td><span class="status <?= $statusClass ?>"><?= htmlspecialchars(ucfirst(strtolower($status))) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;color:var(--muted)">Nenhum agendamento encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>