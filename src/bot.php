<?php declare(strict_types=1);
/* 
the project created by @wizardloop                                                                                                                                                                                                                                                     
*/

$autoload = __DIR__.'/../vendor/autoload.php';
if (!file_exists($autoload)) {
    die("Autoload file not found. Please run 'composer install'.");
}
require_once $autoload;

use JewishPulse\Handlers\AdminPanel;
use JewishPulse\Handlers\ShabatTimes;
use JewishPulse\Payments\Payments;
use JewishPulse\Handlers\Handlers;
use JewishPulse\Locales\Lang; 
use JewishPulse\Storage\UserLocation;

use danog\MadelineProto\Broadcast\Filter;
use danog\MadelineProto\Broadcast\Progress;
use danog\MadelineProto\Broadcast\Status;
use danog\MadelineProto\EventHandler\Attributes\Cron;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Filter\FilterCommandCaseInsensitive;
use danog\MadelineProto\EventHandler\Message\ChannelMessage;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\Message\GroupMessage;
use danog\MadelineProto\EventHandler\SimpleFilter\FromAdmin;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\ParseMode;
use danog\MadelineProto\Settings;
use danog\MadelineProto\SimpleEventHandler;
use danog\MadelineProto\EventHandler\CallbackQuery;
use danog\MadelineProto\EventHandler\Filter\FilterButtonQueryData;
use danog\MadelineProto\EventHandler\Filter\Combinator\FiltersOr;
use Amp\File;

use Amp\Loop; 
use Amp\Promise;
use Amp\Delayed;


class JewishPulse extends SimpleEventHandler 
{

public function getReportPeers() {
return array_map('trim', explode(',', parse_ini_file(__DIR__.'/.env')['ADMIN']));
    }

 #[Handler]
public function ChannelsLeave(ChannelMessage & GroupMessage $message): void { 
	try {
$this->channels->leaveChannel(channel: $message->chatId ); 
} catch (Throwable $e) {}
}

 #[FilterCommandCaseInsensitive('start')]
public function StartCommand(Incoming & PrivateMessage  $message): void {
(new Handlers($this))->starthandle($message);
}

#[FiltersOr(
new FilterButtonQueryData('BackStart'), 
new FilterButtonQueryData('ShabbatTimes'), 
new FilterButtonQueryData('DailyPage'), 
new FilterButtonQueryData('AllCommands'), 
new FilterButtonQueryData('BasicRules'), 
new FilterButtonQueryData('SettingsInfo'), 
new FilterButtonQueryData('CommandsAllUsers'), 
new FilterButtonQueryData('CloseMsgCommand'), 
new FilterButtonQueryData('SettingsMenu'), 
new FilterButtonQueryData('SettingsLocation'), 
new FilterButtonQueryData('SettingsLanguage'), 
new FilterButtonQueryData('SettingsCandles'), 
new FilterButtonQueryData('SettingsHavdalah'), 
)]
public function CallbackHandlers(CallbackQuery $query): void {
$Handle = new Handlers($this);
$senderId = $query->userId;
$data = $query->data;
$msgid = $query->messageId;  

    match ($data) {
  'BackStart' => $Handle->BackStart($senderId, $msgid),
  'ShabbatTimes' => $Handle->ShabbatTimes($senderId, $msgid),
  'DailyPage' => $Handle->DailyPage($senderId, $msgid),
  'AllCommands' => $Handle->AllCommands($senderId, $msgid),
  'BasicRules' => $Handle->BasicRules($senderId, $msgid),
  'SettingsInfo' => $Handle->SettingsInfo($senderId, $msgid),
  'CommandsAllUsers' => $Handle->CommandsAllUsers($senderId, $msgid),
  'CloseMsgCommand' => $Handle->CloseMsgCommand($msgid),
  'SettingsMenu' => $Handle->SettingsMenu($senderId, $msgid),
  'SettingsLocation' => $Handle->SettingsLocation($senderId, $msgid),
  'SettingsLanguage' => $Handle->SettingsLanguage($senderId, $msgid),
  'SettingsCandles' => $Handle->SettingsCandles($senderId, $msgid),
  'SettingsHavdalah' => $Handle->SettingsHavdalah($senderId, $msgid),
    default => null,
    };

}

 #[FilterCommandCaseInsensitive('shabat')]
public function shabatCommand(Incoming $message): void {
(new Handlers($this))->Shabbat_Times($message);
}

 #[FilterCommandCaseInsensitive('stats')]
public function StatsGroups(Incoming $message): void {
(new Handlers($this))->StatsGroups($message);
}

#[Handler]
public function handleSetLanguage(CallbackQuery $query): void {
    try {
            $callbackData = $query->data; 
            $senderId = $query->userId; 
			
$bot_API_markup[] = [['text'=> "🔙", 'callback_data'=> "SettingsMenu"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

        if (strpos($callbackData, 'setlang:') === 0) {

            $lang = explode(':', $callbackData)[1];
            $langSaved = (new Lang($this))->setUserLang($senderId, $lang);
            $lang = (new Lang($this))->getUserLang($senderId);
            $translate = (new Lang($this))->loadTranslations($lang); 
            $msg = $translate['language_selected'] ?? "Language changed.";

            if ($langSaved) {
$query->editText(
$message = $msg, 
$replyMarkup = $bot_API_markup, 
ParseMode::HTML
);
            } else {
$query->editText(
$message = "⚠️ unable to preserve the language.", 
$replyMarkup = $bot_API_markup, 
ParseMode::HTML
);
            }
        }
    } catch (\Throwable $e) {
$query->editText(
$message = "⚠️ " . $e->getMessage(), 
$replyMarkup = $bot_API_markup, 
ParseMode::HTML
);
    }
}

#[Handler]
public function handleSetCandles(CallbackQuery $query): void {
    try {
        $callbackData = $query->data;
        $senderId = $query->userId;

        $bot_API_markup[] = [['text'=> "🔙", 'callback_data'=> "SettingsMenu"]];
        $bot_API_markup = ['inline_keyboard'=> $bot_API_markup,];

        if (strpos($callbackData, 'setcandles_') === 0) {

            $candlesMinutes = (int)explode('_', $callbackData)[1];

$candlesSaved = UserLocation::setCandleSetting($senderId, $candlesMinutes);

            $lang = (new Lang($this))->getUserLang($senderId);
            $translate = (new Lang($this))->loadTranslations($lang);

                $msg = $translate['settings_candles_save'] ?? "Candle setting saved!";
                $query->editText(
                    $message = $msg,
                    $replyMarkup = $bot_API_markup,
                    ParseMode::HTML
                );

        }
    } catch (\Throwable $e) {
        $query->editText(
            $message = "⚠️ " . $e->getMessage(),
            $replyMarkup = $bot_API_markup,
            ParseMode::HTML
        );
    }
}

#[Handler]
public function handleSetHavdalah(CallbackQuery $query): void {
    try {
        $callbackData = $query->data;
        $senderId = $query->userId;

        $bot_API_markup[] = [['text'=> "🔙", 'callback_data'=> "SettingsMenu"]];
        $bot_API_markup = ['inline_keyboard'=> $bot_API_markup,];

        if (strpos($callbackData, 'sethavdalah_') === 0) {

            $havdalahTime = (float)explode('_', $callbackData)[1];

$havdalahSaved = UserLocation::setHavdalahSetting($senderId, $havdalahTime);

            $lang = (new Lang($this))->getUserLang($senderId);
            $translate = (new Lang($this))->loadTranslations($lang);

                $msg = $translate['settings_havdalah_save'] ?? "Havdalah setting saved!";
                $query->editText(
                    $message = $msg,
                    $replyMarkup = $bot_API_markup,
                    ParseMode::HTML
                );
        }
    } catch (\Throwable $e) {
        $query->editText(
            $message = "⚠️ " . $e->getMessage(),
            $replyMarkup = $bot_API_markup,
            ParseMode::HTML
        );
    }
}

####### INLINE #######
public function onUpdateBotInlineQuery($update) {
    try {
        $query = trim($update['query']);
        $userid = $update['user_id'];
		
        if (strlen($query) >= 2 && !in_array($query, ['shabat', 'shabbat', 'שבת'])) {
		
$lang = (new Lang($this))->getUserLang($userid);
$translate = (new Lang($this))->loadTranslations($lang); 
$location_text = $translate['location_text'] ?? 'location';
$location_set = $translate['location_set'] ?? 'Set this location as your region';

$locations = UserLocation::searchGeoNames($query);

$results = [];
foreach ($locations as $index => $location) {
	$resultId = 'geo_' . $index;
    $geonameid = $location['geonameId'];
    $title = $location['name'] . ', ' . ($location['countryName'] ?? '');
    $lat = $location['lat'];
    $lng = $location['lng'];

$messageText = "$location_text $geonameid [$title]";

    $botInlineMessageText = [
        '_' => 'inputBotInlineMessageText',
        'message' => $messageText,
        'parse_mode' => 'Markdown'
    ];

    $results[] = [
        '_' => 'botInlineResult',
        'id' => (string)$resultId,
        'type' => 'article',
        'title' => $title,
        'description' => $location_set,
        'send_message' => $botInlineMessageText
    ];
}

            $this->messages->setInlineBotResults([
                'query_id' => $update['query_id'],
                'results' => $results,
                'cache_time' => 0
            ]);
            return;
        }

        if (in_array($query, ['shabat', 'shabbat', 'שבת'])) {
	
$geonameId = UserLocation::getGeonameId($update['user_id']);
$geonameId = (int) $geonameId;

$candles = UserLocation::getCandleSetting($update['user_id']);
$candles = (int) $candles;

$havdalah = UserLocation::getHavdalahSetting($update['user_id']);
$havdalah = (int) $havdalah;
     
        if ($geonameId) {
			$senderId = $userid;
           $zmanim = ShabatTimes::parseShabatData($geonameId, $candles, $havdalah, $senderId, $this);
        } else {
           $zmanim = "Geoname ID not found";
        }

$lang = (new Lang($this))->getUserLang($userid);
$translate = (new Lang($this))->loadTranslations($lang); 
$share_shabbat_times = $translate['share_shabbat_times'] ?? 'share_shabbat_times';
$share_shabbat_times2 = $translate['share_shabbat_times2'] ?? 'share_shabbat_times2';
$share_shabbat_times3 = $translate['share_shabbat_times3'] ?? 'share_shabbat_times3';
$update_channel = $translate['update_channel'] ?? 'update_channel';

$me = $this->getSelf();
$me_username = $me['username'];

$inlineQueryPeerTypePM = ['_' => 'inlineQueryPeerTypePM'];
$inlineQueryPeerTypeChat = ['_' => 'inlineQueryPeerTypeChat'];
$inlineQueryPeerTypeBotPM = ['_' => 'inlineQueryPeerTypeBotPM'];
$inlineQueryPeerTypeMegagroup = ['_' => 'inlineQueryPeerTypeMegagroup'];
$inlineQueryPeerTypeBroadcast = ['_' => 'inlineQueryPeerTypeBroadcast'];

$keyboardButtonSwitchInline = ['_' => 'keyboardButtonSwitchInline', 'same_peer' => false, 'text' => $share_shabbat_times, 'query' => 'shabat', 'peer_types' => [$inlineQueryPeerTypePM, $inlineQueryPeerTypeChat, $inlineQueryPeerTypeBotPM, $inlineQueryPeerTypeMegagroup, $inlineQueryPeerTypeBroadcast]];
$keyboardButtonUrl = ['_' => 'keyboardButtonUrl', 'text' => $update_channel, 'url' => 'https://t.me/JewishPulse'];
$keyboardButtonRow1 = ['_' => 'keyboardButtonRow', 'buttons' => [$keyboardButtonSwitchInline]];
$keyboardButtonRow2 = ['_' => 'keyboardButtonRow', 'buttons' => [$keyboardButtonUrl]];
$bot_API_markup = ['_' => 'replyInlineMarkup', 'rows' => [$keyboardButtonRow1, $keyboardButtonRow2]];

$documentAttributeImageSize = ['_' => 'documentAttributeImageSize', 'w' => 475, 'h' => 475];
$inputWebDocument = ['_' => 'inputWebDocument', 'url' => 'https://i.imgur.com/QLV68NE.png', 'size' => 98166, 'mime_type' => 'image/jpeg', 'attributes' => [$documentAttributeImageSize]];

$botInlineMessageText = ['_' => 'inputBotInlineMessageText', 'message' => "$zmanim", 'parse_mode'=> 'MARKDOWN', 'reply_markup' => $bot_API_markup];
$inputBotInlineResult = ['_' => 'botInlineResult', 'id' => '0', 'type' => 'article', 'title' => $share_shabbat_times2, 'description' => $share_shabbat_times3, 'thumb' => $inputWebDocument,'send_message' => $botInlineMessageText];
	
            $result = ['query_id' => $update['query_id'], 'results' => [$inputBotInlineResult], 'cache_time' => 0];

            $this->messages->setInlineBotResults($result);
        }

    } catch (Throwable $e) {
echo $e->getMessage();
}
}
#[Handler]
public function handleSaveLocationFromMessage(Incoming & PrivateMessage $message): void {
    try {
        $text = $message->message;
        $userId = $message->chatId;

$lang = (new Lang($this))->getUserLang($userId);
$translate = (new Lang($this))->loadTranslations($lang); 
$location_text = $translate['location_text'] ?? 'location';
$location_save = $translate['location_save'] ?? '✅ Location saved successfully!';

        if (preg_match("/$location_text/", $text)) { 
            $parts = explode(':', $text);
            $geonameId = (int)$parts[1];
            UserLocation::setGeonameId($userId, $geonameId);

$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $message->id];
            $this->messages->sendMessage([
                'peer' => $message->chatId,
                'message' => $location_save,
                'reply_to' => $inputReplyToMessage,
                'parse_mode' => 'HTML'
            ]);
        }
        
    } catch (Throwable $e) {
        echo $e->getMessage();
    }
}


####### PAYMENTS #######
#[FilterCommandCaseInsensitive('donate')]
public function Payments(Incoming & PrivateMessage $message): void {
(new Payments($this))->sendDonationOptions(senderId: $message->senderId, replyToMsgId: $message->id);
}
public function onupdateBotPrecheckoutQuery($update): void {
(new Payments($this))->handlePreCheckout($update);
}


####### ADMIN _ COMMANDS #######
#[FilterCommandCaseInsensitive('admin')]
public function AdminCommand(Incoming & PrivateMessage & FromAdmin $message): void {
(new AdminPanel($this))->handle($message);
}

#[FiltersOr(
new FilterButtonQueryData('admin_back'), 
new FilterButtonQueryData('admin_stats'), 
new FilterButtonQueryData('admin_broadcast'), 
new FilterButtonQueryData('broadcast_back'),
new FilterButtonQueryData('back_broadcast'),
new FilterButtonQueryData('type_users'),
new FilterButtonQueryData('typemode1'),
new FilterButtonQueryData('typemode2'),
new FilterButtonQueryData('typemode3'),
new FilterButtonQueryData('typemode4'),
)]
    public function handleAdminButtons(CallbackQuery $query): void {
    $admin = new AdminPanel($this);
    $senderId = $query->userId;
    $data = $query->data;
    $msgid = $query->messageId;  
	
    match ($data) {
  'admin_stats' => $admin->statUsers($senderId, $msgid),
  'admin_back' => $admin->admin_back($senderId, $msgid),
  'broadcast_back' => $admin->broadcast_back($senderId, $msgid),
  'admin_broadcast' => $admin->admin_broadcast($senderId, $msgid),
  'back_broadcast' => $admin->back_broadcast($senderId, $msgid),
  'type_users' => $admin->type_users($senderId, $msgid),
  'typemode1' => $admin->typemode1($senderId, $msgid),
  'typemode2' => $admin->typemode2($senderId, $msgid),
  'typemode3' => $admin->typemode3($senderId, $msgid),
  'typemode4' => $admin->typemode4($senderId, $msgid),
    default => null,
    };

}

    #[Handler]
   public function handlebroadcast1(Incoming & PrivateMessage & FromAdmin $message): void {
(new AdminPanel($this))->handle_broadcast($message);
	}

  #[FilterButtonQueryData('send_broadcast')]
 public function send_broadcast(callbackQuery $query) {
try {
$senderId = $query->userId;
$data = $query->data;
$msgid = $query->messageId;  
	
$lang = (new Lang($this))->getUserLang($senderId);
$translate = (new Lang($this))->loadTranslations($lang); 
$txt = $translate['broadcast_sender'] ?? 'broadcast_sender';

try { 
$this->messages->deleteMessages(revoke: true, id: [$msgid]); 
} catch (Throwable $e) { }
	
$sentMessage = $this->messages->sendMessage(
 peer: $senderId,
 message: $txt
);

$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write(__DIR__."/data/messagetoeditbroadcast1.txt", "$sentMessage2");
Amp\File\write(__DIR__."/data/messagetoeditbroadcast2.txt", "$senderId");

 if (file_exists(__DIR__."/data/$senderId/txt.txt")) {
$filexmsgidtxt = Amp\File\read(__DIR__."/data/$senderId/txt.txt");  
}else{
$filexmsgidtxt = null; 
}
  if (file_exists(__DIR__."/data/$senderId/ent.txt")) {
$filexmsgident = json_decode(Amp\File\read(__DIR__."/data/$senderId/ent.txt"),true);  
  }else{
$filexmsgident = null;  
  }	  
  if (file_exists(__DIR__."/data/$senderId/media.txt")) {
$filexmsgidmedia = Amp\File\read(__DIR__."/data/$senderId/media.txt");  
  }else{
$filexmsgidmedia = null;  
  }	 

    if (file_exists(__DIR__."/data/broadcastsend.txt")) {
$check2 = Amp\File\read(__DIR__."/data/broadcastsend.txt");    
if($check2 == "USERS"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$broadcastId = $this->broadcastMessages(
messages: [['media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "CHANNELS"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$broadcastId = $this->broadcastMessages(
messages: [['media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "GROUPS"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$broadcastId = $this->broadcastMessages(
messages: [['media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "ALL"){

if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$broadcastId = $this->broadcastMessages(
messages: [['media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}	
}
    if (!file_exists(__DIR__."/data/broadcastsend.txt")) {

if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$broadcastId = $this->broadcastMessages(
messages: [['media' => $filexmsgidmedia]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$broadcastId = $this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}

} catch (Throwable $e) {}
}

private int $lastLog = 0;
    #[Handler]
   public function handleBroadcastProgress(Progress $progress): void {
		try {
$progressStr = (string) $progress;

if (time() - $this->lastLog > 5 || $progress->status === Status::GATHERING_PEERS) {
            $this->lastLog = time();
 if (file_exists(__DIR__."/data/messagetoeditbroadcast2.txt")) {
$filexmsgid1 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast2.txt");  
 if (file_exists(__DIR__."/data/messagetoeditbroadcast1.txt")) {
$filexmsgid2 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast1.txt");  
			try {
$this->messages->editMessage(peer: $filexmsgid1, id: $filexmsgid2, message: "⏳ $progressStr", reply_markup: null);
} catch (Throwable $e) {}
}
}
}

if (time() - $this->lastLog > 5 || $progress->status === Status::FINISHED) {
            $this->lastLog = time();
if (file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = Amp\File\read(__DIR__."/data/broadcastsend.txt");
}
if (!file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = "ALL";
}

$pendingCount = $progress->pendingCount;
$sucessCount = $progress->successCount;
$sucessCount2 = $progress->failCount;

 if (file_exists(__DIR__."/data/messagetoeditbroadcast2.txt")) {
$filexmsgid1 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast2.txt");  

 if (file_exists(__DIR__."/data/messagetoeditbroadcast1.txt")) {
$filexmsgid2 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast1.txt");  

$bot_API_markup = ['inline_keyboard' => [[['text'=>"🔙",'callback_data'=>"admin_back"]]]];

			try {
$this->messages->editMessage(peer: $filexmsgid1, id: $filexmsgid2, message: "✅ $sucessCount
⏳ $pendingCount
❌ $sucessCount2", reply_markup: $bot_API_markup);
} catch (Throwable $e) {}

 if (file_exists(__DIR__."/data/$filexmsgid1/txt.txt")) {
unlink(__DIR__."/data/$filexmsgid1/txt.txt");  
}
  if (file_exists(__DIR__."/data/$filexmsgid1/ent.txt")) {
unlink(__DIR__."/data/$filexmsgid1/ent.txt");  
  }	  
  if (file_exists(__DIR__."/data/$filexmsgid1/media.txt")) {
unlink(__DIR__."/data/$filexmsgid1/media.txt");  
  }	 

 }
 }
 }
} catch (Throwable $e) {}
}

}

function runJewishPulse(string $apiId, string $apiHash, string $botToken): void {
    try {
        $settings = new Settings;
        $settings->setAppInfo(
            (new \danog\MadelineProto\Settings\AppInfo)
                ->setApiId((int)$apiId)
                ->setApiHash($apiHash)
        );

        JewishPulse::startAndLoopBot(__DIR__ . '/bot.madeline', $botToken, $settings);

    } catch (Throwable $e) {
        echo "\nError: " . $e->getMessage() . "\n";
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    die("Please run the bot from 'main.php'.");
}
