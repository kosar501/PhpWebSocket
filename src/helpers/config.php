<?php
function config($key = null) {
    static $config = [];

    // If the config is not loaded yet, load it
    if (empty($config)) {
        // Path to the config directory
        $configPath = __DIR__ . '/../configs/';

        // Load each PHP config file from the directory
        foreach (glob($configPath . '*.php') as $file) {
            $configKey = basename($file, '.php'); // Use filename as config key (e.g., 'websocket')
            $config[$configKey] = require $file;
        }
    }

    // If no specific key is passed, return all configurations
    if ($key === null) {
        return $config;
    }

    // Handle dot notation to retrieve nested keys
    $keys = explode('.', $key);
    $value = $config;
    foreach ($keys as $part) {
        if (isset($value[$part])) {
            $value = $value[$part];
        } else {
            return null; // Return null if the key doesn't exist
        }
    }

    return $value;
}
