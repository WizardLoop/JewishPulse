<?php declare(strict_types=1);
/* 
the project created by @wizardloop                                                                                                                                                                                                                                                     
*/
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/bot.php';

$env = parse_ini_file(__DIR__ . '/src/.env');

runJewishPulse($env['API_ID'], $env['API_HASH'], $env['BOT_TOKEN']);