<?php declare(strict_types=1);

/*
 * The project created by @wizardloop
 */

require_once __DIR__ . '/vendor/autoload.php';

use JewishPulse\JewishPulse;

$env = parse_ini_file(__DIR__ . '/src/.env');

if (!isset($env['API_ID'], $env['API_HASH'], $env['BOT_TOKEN'])) {
    die("Missing environment variables in .env\n");
}

$apiId   = $env['API_ID'];
$apiHash = $env['API_HASH'];
$botToken = $env['BOT_TOKEN'];

try {
    $settings = new \danog\MadelineProto\Settings();
    $settings->setAppInfo(
        (new \danog\MadelineProto\Settings\AppInfo())
            ->setApiId((int) $apiId)
            ->setApiHash($apiHash)
    );

    JewishPulse::startAndLoopBot(
        __DIR__ . '/src/bot.madeline',
        $botToken,
        $settings
    );
} catch (\Throwable $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    exit(1);
}
