<?php
require_once __DIR__ . '/../../config.php';

function env(string $key, mixed $default = null): mixed
{
    return defined($key) ? constant($key) : $default;
}

function isProduction(): bool
{
    return env('APP_ENV') === 'production';
}
