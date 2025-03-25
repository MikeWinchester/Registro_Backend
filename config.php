<?php
function loadEnv($file = __DIR__ . "/.env") {
    $env = [];

    $azureEnvKeys = ["DB_HOST", "DB_USER", "DB_PASS", "DB_NAME", "SECRET_KEY"];
    $azureEnv = [];

    foreach ($azureEnvKeys as $key) {
        $value = getenv($key);
        if ($value !== false) {
            $azureEnv[$key] = $value;
        }
    }

    if (!empty($azureEnv)) {
        return $azureEnv;
    }

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), "#") === 0) {
                continue;
            }
            list($key, $value) = explode("=", $line, 2);
            $env[trim($key)] = trim($value);
        }
    }

    return $env;
}

return loadEnv();
?>
