<?php

$botToken = getenv('TELEGRAM_BOT_TOKEN');
$chatId = getenv('TELEGRAM_CHAT_ID');
$releaseName = getenv('RELEASE_NAME');
$releaseTag = getenv('RELEASE_TAG');
$releaseBody = getenv('RELEASE_BODY');
$releaseUrl = "https://github.com/" . getenv('GITHUB_REPOSITORY') . "/releases/tag/" . $releaseTag;

$message = "🚀 *New Release: $releaseName*

";
$message .= "🔖 *Version:* `$releaseTag`
";
$message .= "📝 *Details:*
$releaseBody";

$url = "https://api.telegram.org/bot$botToken/sendMessage";

$postFields = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
