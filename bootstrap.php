<?php
// bootstrap.php
// Inicializa recursos compartilhados da aplicação (ex.: conexão com DB)

// carregar Database config/loader
require_once __DIR__ . '/Config/Database.php';

$pdo = null;
$dbError = null;
try {
    $pdo = Database::getConnection();
} catch (Exception $e) {
    $dbError = $e->getMessage();
    error_log('Bootstrap DB connection failed: ' . $dbError);
    $pdo = null; // mantém código resistente a falhas
}

/**
 * Helper para obter a instância PDO (ou null se não conectou)
 */
function db()
{
    global $pdo;
    return $pdo;
}

// Marca que o bootstrap foi carregado
if (!defined('BOOTSTRAP_INCLUDED')) {
    define('BOOTSTRAP_INCLUDED', true);
}
