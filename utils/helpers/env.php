<?php
function env(string $key, mixed $default = null): mixed {
    return getenv($key) ?: $default;
}

function isProduction(): bool {
    return env('APP_ENV') === 'production';
}
