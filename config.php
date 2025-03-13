<?php
function loadEnv($file = __DIR__ . "/.env") {
    $env = [];

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), "#") === 0) {
                continue;
            }

            list($key, $value) = explode("=", $line, 2);
            $env[trim($key)] = trim($value);
        }
    } else {
        // Si el archivo .env no existe, usar las variables de entorno del sistema (Azure App Services)
        foreach ($_ENV as $key => $value) {
            $env[$key] = $value;
        }

        foreach ($_SERVER as $key => $value) {
            if (!isset($env[$key])) {
                $env[$key] = $value;
            }
        }
    }

    return $env;
}

return loadEnv();
?>

