<?php
session_start();

// Verifica método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: Index.php');
	exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? (string)$_POST['senha'] : '';

// Usuários de exemplo (substituir por consulta ao banco quando disponível)
$usuarios = [
	// email => [senha, role]
	'cliente@mybeauty.com' => ['123456', 'cliente'],
	'func@mybeauty.com'    => ['123456', 'funcionario'],
];

$usuarioAutenticado = null;
if (isset($usuarios[$email]) && hash_equals($usuarios[$email][0], $senha)) {
	$usuarioAutenticado = [
		'email' => $email,
		'role'  => $usuarios[$email][1],
	];
}

if (!$usuarioAutenticado) {
	header('Location: Index.php?erro=1');
	exit;
}

$_SESSION['usuario'] = $usuarioAutenticado;

// Redireciona por perfil
if ($usuarioAutenticado['role'] === 'cliente') {
	header('Location: Views/dashboard_cliente.php');
	exit;
}

header('Location: Views/dashboard_funcionario.php');
exit;
