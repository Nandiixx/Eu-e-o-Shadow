<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: Index.php');
	exit;
}

$required = ['nome','email','numero','data_nascimento','senha','confirma_senha'];
foreach ($required as $field) {
	if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') {
		header('Location: Views/cliente/cadastrar.php?erro=1');
		exit;
	}
}

if ((string)$_POST['senha'] !== (string)$_POST['confirma_senha']) {
	header('Location: Views/cliente/cadastrar.php?erro=2');
	exit;
}

// TODO: substituir por inserção real no banco
// Por enquanto, simula sucesso e volta para o login
header('Location: Index.php?cadastro=ok');
exit;
