<?php
function loadEnv($file = __DIR__ . "/.env") {
    if (!file_exists($file)) {
        die("Archivo .env no encontrado");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        if (strpos(trim($line), "#") === 0) {
            continue;
        }

        list($key, $value) = explode("=", $line, 2);
        $env[trim($key)] = trim($value);
    }

    return $env;
}

return loadEnv();
?>
