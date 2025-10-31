<?php
/**
 * Simple Database loader that reads connection values from a .env file
 * and returns a PDO instance. Keeps credentials out of application code.
 *
 * .env should be located at the project root (one level above Config/)
 */
class Database
{
    protected static $env = null;

    protected static function loadEnv($path = null)
    {
        if (self::$env !== null) return self::$env;
        $env = [];
        if ($path === null) {
            $path = __DIR__ . '/../.env';
        }
        if (!file_exists($path)) {
            self::$env = $env;
            return $env;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // remove optional quotes
            if ((strlen($value) >= 2) && (($value[0] === '"' && $value[strlen($value)-1] === '"') || ($value[0] === "'" && $value[strlen($value)-1] === "'"))) {
                $value = substr($value, 1, -1);
            }
            $env[$key] = $value;
        }
        self::$env = $env;
        return $env;
    }

    public static function env(string $key, $default = null)
    {
        $e = self::loadEnv();
        return $e[$key] ?? $default;
    }

    /**
     * Returns a PDO connection using credentials from .env
     * Throws exception on connection errors.
     */
    public static function getConnection(): PDO
    {
        $host = self::env('DB_HOST', '127.0.0.1');
        $name = self::env('DB_NAME', 'tcc');
        $user = self::env('DB_USER', 'root');
        $pass = self::env('DB_PASS', '');
        $charset = self::env('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }
}
