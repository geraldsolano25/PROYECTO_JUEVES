<?php
$config = [
    'enabled' => getenv('MAIL_ENABLED') === 'true',
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'username' => getenv('MAIL_USERNAME') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
    'port' => (int) (getenv('MAIL_PORT') ?: 587),
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'from_email' => getenv('MAIL_FROM_EMAIL') ?: getenv('MAIL_USERNAME'),
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'AlertaComunal',
    'debug_log' => __DIR__ . '/../storage/mail.log',
];

$local = __DIR__ . '/mail.local.php';
if (file_exists($local)) {
    $config = array_merge($config, require $local);
}

return $config;
