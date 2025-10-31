<?php
/**
 * Inclusão inteligente do bootstrap.php
 * Para usar, adicione no topo do arquivo:
 * require_once __DIR__ . '/../includes/db_include.php'; (ajuste o caminho conforme necessário)
 */

if (!defined('BOOTSTRAP_INCLUDED')) {
    $possiblePaths = [
        __DIR__ . '/../bootstrap.php',
        __DIR__ . '/../../bootstrap.php',
        __DIR__ . '/../../../bootstrap.php',
        __DIR__ . '/bootstrap.php'
    ];

    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }

    if (!defined('BOOTSTRAP_INCLUDED')) {
        error_log('WARNING: bootstrap.php não encontrado. Alguns recursos podem não funcionar.');
    }
}