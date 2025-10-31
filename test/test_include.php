<?php
/**
 * Arquivo de teste para verificar a inclusão automática do bootstrap.php
 */

require_once __DIR__ . '/../includes/db_include.php';

// Se o bootstrap foi incluído com sucesso, devemos ter acesso à conexão do banco de dados
if (defined('BOOTSTRAP_INCLUDED')) {
    echo "Bootstrap incluído com sucesso!\n";
    
    try {
        $db = Database::getConnection();
        echo "Conexão com o banco de dados estabelecida com sucesso!\n";
    } catch (Exception $e) {
        echo "Erro ao conectar com o banco de dados: " . $e->getMessage() . "\n";
    }
} else {
    echo "ERRO: bootstrap.php não foi incluído corretamente.\n";
}