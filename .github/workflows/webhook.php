<?php

$telegram_bot_token = getenv('TELEGRAM_BOT_TOKEN');
$telegram_chat_id = getenv('TELEGRAM_CHAT_ID');
$github_repo_owner = getenv('REPO_OWNER');
$github_repo_name = getenv('REPO_NAME');

function send_to_telegram($message) {
    global $telegram_bot_token, $telegram_chat_id;

    $url = "https://api.telegram.org/bot$telegram_bot_token/sendMessage";
    $data = [
        'chat_id' => $telegram_chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => false,
    ];

    $opts = [
        "http" => [
            "method" => "POST",
            "header" => "Content-type: application/x-www-form-urlencoded",
            "content" => http_build_query($data)
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        error_log("Error sending message to Telegram.");
    } else {
        echo "Message sent successfully.\n";
    }
}

function get_release_info($repo_owner, $repo_name, $tag) {
    $url = "https://api.github.com/repos/$repo_owner/$repo_name/releases/tags/$tag";

    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: PHP"
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return null;
    }

    $release_info = json_decode($response, true);

    if (isset($release_info['tag_name'], $release_info['name'], $release_info['body'])) {
        return [
            'tag' => $release_info['tag_name'],
            'name' => $release_info['name'],
            'body' => $release_info['body']
        ];
    }

    return null;
}

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (isset($data['action'], $data['release']) && $data['action'] === 'published') {
    $release_tag = $data['release']['tag_name'];
    $release_name = $data['release']['name'];
    $release_body = $data['release']['body'];

    $release_info = get_release_info($github_repo_owner, $github_repo_name, $release_tag);

    if ($release_info) {

        $message = "🎉 **NEW VERSION • " . $release_info['name'] . "**\n\n";
        $message .= "📝 [Changelog](https://github.com/$github_repo_owner/$github_repo_name/releases/tag/$release_tag)\n\n";
        $message .= $release_info['body'];

        send_to_telegram($message);
    }
} else {
    error_log("No release data found or incorrect action type.");
}

?>
