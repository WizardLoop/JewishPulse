<?php
/* 
the project created by @wizardloop                                                                                                                                                                                                                                                     
*/
namespace JewishPulse\Handlers;

use JewishPulse\Locales\Lang;
use JewishPulse\Storage\UserLocation;
use JewishPulse\Handlers\ShabatTimes;

use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;

use Amp\File;
use function Amp\File\write;
use function Amp\File\read;
use function Amp\File\exists;

class Handlers
{
    private object $context;
    public function __construct(object $context) {
        $this->context = $context;
    }

    public function starthandle(Incoming & PrivateMessage $message): void {
		try{
        $senderId = $message->senderId;
        $messageid = $message->id;
		$me = $this->context->getSelf();
        $me_username = $me['username'];
		
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$welcome = $translate['welcome'] ?? 'welcome';
$welcome_button1_txt = $translate['welcome_button1_txt'] ?? 'welcome_button1_txt';
$welcome_button1_data = $translate['welcome_button1_data'] ?? 'welcome_button1_data';
$welcome_button4_txt = $translate['welcome_button4_txt'] ?? 'welcome_button4_txt';
$welcome_button4_data = $translate['welcome_button4_data'] ?? 'welcome_button4_data';
$welcome_button3_txt = $translate['welcome_button3_txt'] ?? 'welcome_button3_txt';
$welcome_button3_data = $translate['welcome_button3_data'] ?? 'welcome_button3_data';
$welcome_button5_txt = $translate['welcome_button5_txt'] ?? 'welcome_button5_txt';
$welcome_button5_data = $translate['welcome_button5_data'] ?? 'welcome_button5_data';

$bot_API_markup[] = [['text'=>$welcome_button1_txt,'callback_data'=>$welcome_button1_data]];
$bot_API_markup[] = [['text'=>$welcome_button4_txt,'callback_data'=>$welcome_button4_data]];
$bot_API_markup[] = [['text'=>$welcome_button3_txt,'callback_data'=>$welcome_button3_data]];
$bot_API_markup[] = [['text'=>$welcome_button5_txt,'callback_data'=>$welcome_button5_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];
$this->context->messages->sendMessage(peer: $message->senderId, reply_to: $inputReplyToMessage, message: $welcome, reply_markup: $bot_API_markup, parse_mode: 'HTML');

    if (!file_exists(__DIR__."/../data")) {
mkdir(__DIR__."/../data");
}
    if (!file_exists(__DIR__."/../data/$senderId")) {
mkdir(__DIR__."/../data/$senderId");
}
if (file_exists(__DIR__."/../data/$senderId/grs1.txt")) {
unlink(__DIR__."/../data/$senderId/grs1.txt");
}
        } catch (\Throwable $e) {}
		}

    public function BackStart(int $senderId, int $msgid): void {
		try{
		$me = $this->context->getSelf();
        $me_username = $me['username'];
		
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$welcome = $translate['welcome'] ?? 'welcome';
$welcome_button1_txt = $translate['welcome_button1_txt'] ?? 'welcome_button1_txt';
$welcome_button1_data = $translate['welcome_button1_data'] ?? 'welcome_button1_data';
$welcome_button4_txt = $translate['welcome_button4_txt'] ?? 'welcome_button4_txt';
$welcome_button4_data = $translate['welcome_button4_data'] ?? 'welcome_button4_data';
$welcome_button3_txt = $translate['welcome_button3_txt'] ?? 'welcome_button3_txt';
$welcome_button3_data = $translate['welcome_button3_data'] ?? 'welcome_button3_data';
$welcome_button5_txt = $translate['welcome_button5_txt'] ?? 'welcome_button5_txt';
$welcome_button5_data = $translate['welcome_button5_data'] ?? 'welcome_button5_data';

$bot_API_markup[] = [['text'=>$welcome_button1_txt,'callback_data'=>$welcome_button1_data]];
$bot_API_markup[] = [['text'=>$welcome_button4_txt,'callback_data'=>$welcome_button4_data]];
$bot_API_markup[] = [['text'=>$welcome_button3_txt,'callback_data'=>$welcome_button3_data]];
$bot_API_markup[] = [['text'=>$welcome_button5_txt,'callback_data'=>$welcome_button5_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $welcome, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);

if (file_exists(__DIR__."/../data/$senderId/grs1.txt")) {
unlink(__DIR__."/../data/$senderId/grs1.txt");
}

        } catch (\Throwable $e) {}
		}

    public function ShabbatTimes(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$shabbat_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$shabbat_back_data = $translate['shabbat_back_data'] ?? 'shabbat_back_data';

$bot_API_markup[] = [['text'=>$shabbat_back_txt,'callback_data'=>$shabbat_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: "⌛️", 
reply_markup: null, 
parse_mode: 'HTML'
);

$geonameId = UserLocation::getGeonameId($senderId);
$geonameId = (int) $geonameId;

$candles = UserLocation::getCandleSetting($senderId);
$candles = (int) $candles;

$havdalah = UserLocation::getHavdalahSetting($senderId);
$havdalah = (int) $havdalah;

        if ($geonameId) {
           $zmanim = ShabatTimes::parseShabatData($geonameId, $candles, $havdalah, $senderId, $this->context);
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $zmanim,
reply_markup: $bot_API_markup, 
parse_mode: 'MARKDOWN'
);	
        } else {
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: "Geoname ID not found",
reply_markup: $bot_API_markup
);	
        }

        } catch (\Throwable $e) {
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $e->getMessage(), 
reply_markup: $bot_API_markup
);			
			
			
		}
		}

    public function Shabbat_Times(Incoming $message): void {
		try{
        $senderId = $message->senderId;
        $messageid = $message->id;
		$chatid = $message->chatId;

$lang = (new Lang($this->context))->getUserLang($chatid);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$share_shabbat_times = $translate['share_shabbat_times'] ?? 'share_shabbat_times';
$update_channel = $translate['update_channel'] ?? 'update_channel';

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

$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];

$sentMessage = $this->context->messages->sendMessage(
peer: $chatid, 
reply_to: $inputReplyToMessage,
message: "⌛️", 
reply_markup: null, 
parse_mode: 'HTML'
);
$sentMessage2 = $this->context->extractMessageId($sentMessage);

$geonameId = UserLocation::getGeonameId($chatid);
$geonameId = (int) $geonameId;

$candles = UserLocation::getCandleSetting($senderId);
$candles = (int) $candles;

$havdalah = UserLocation::getHavdalahSetting($senderId);
$havdalah = (int) $havdalah;
 
        if ($geonameId) {
           $zmanim = ShabatTimes::parseShabatData($geonameId, $candles, $havdalah, $senderId, $this->context);
$this->context->messages->editMessage(
peer: $chatid, 
id: $sentMessage2, 
message: $zmanim,
reply_markup: $bot_API_markup, 
parse_mode: 'MARKDOWN'
);	
        } else {
$this->context->messages->editMessage(
peer: $chatid, 
id: $sentMessage2, 
message: "Geoname ID not found",
reply_markup: null
);	
        }


        } catch (\Throwable $e) {
			
$this->context->messages->editMessage(
peer: $chatid, 
id: $sentMessage2, 
message: $e->getMessage(), 
reply_markup: null
);	


		}
		}

   public function DailyPage(int $senderId, int $msgid): void {
    try {
        $lang = (new Lang($this->context))->getUserLang($senderId);
        $translate = (new Lang($this->context))->loadTranslations($lang); 
        $daily_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
        $daily_back_data = $translate['shabbat_back_data'] ?? 'shabbat_back_data';

        $bot_API_markup[] = [['text'=>$daily_back_txt, 'callback_data'=>$daily_back_data]];
        $bot_API_markup = ['inline_keyboard' => $bot_API_markup];

        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: "⌛️", 
            reply_markup: null, 
            parse_mode: 'HTML'
        );

        $geonameId = UserLocation::getGeonameId($senderId);
        $geonameId = (int) $geonameId;

        if ($geonameId) {
            $dailyPage = ShabatTimes::getDailyPage($geonameId, $senderId); 
            $this->context->messages->editMessage(
                peer: $senderId, 
                id: $msgid, 
                message: $dailyPage,
                reply_markup: $bot_API_markup, 
                parse_mode: 'MARKDOWN'
            );  
        } else {
            $this->context->messages->editMessage(
                peer: $senderId, 
                id: $msgid, 
                message: "Geoname ID לא נמצא",
                reply_markup: $bot_API_markup
            );  
        }
    } catch (\Throwable $e) {
        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: $e->getMessage(), 
            reply_markup: $bot_API_markup
        );  
    }
}

    public function StatsGroups(Incoming $message): void {
		try{
        $senderId = $message->senderId;
        $messageid = $message->id;
		$chatid = $message->chatId;

$lang = (new Lang($this->context))->getUserLang($chatid);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$stats_groups = $translate['stats_groups'] ?? 'stats_groups';

$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];

$sentMessage = $this->context->messages->sendMessage(
peer: $chatid, 
reply_to: $inputReplyToMessage,
message: "⌛️"
);
$sentMessage2 = $this->context->extractMessageId($sentMessage);

$dialogs = $this->context->getDialogIds();
$numFruits = count($dialogs);
$peerList31 = [];
foreach($dialogs as $peer)
{
try {
$info = $this->context->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "supergroup"){
continue;
}
$peerList31[]=$peer;
} catch (Throwable $e) {
continue;
}
}
$numFruits31 = count($peerList31);

$peerList312 = [];
foreach($dialogs as $peer)
{
	try {
$info = $this->context->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "chat"){
continue;
}
$peerList312[]=$peer;
} catch (Throwable $e) {
continue;
}
}
$numFruits312 = count($peerList312);

if (!isset($numFruits312)) {
$numFruits312 = 0;
} else {
}
if (!isset($numFruits31)) {
$numFruits31 = 0;
} else {
}
$numFruits3new = $numFruits312 + $numFruits31;

$this->context->messages->editMessage(
peer: $chatid, 
id: $sentMessage2, 
message: "$stats_groups <code>$numFruits3new</code>", 
parse_mode: 'HTML'
);	

        } catch (\Throwable $e) {
$this->context->messages->editMessage(
peer: $chatid, 
id: $sentMessage2, 
message: $e->getMessage()
);	
		}
		}

    public function AllCommands(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$helptext = $translate['help_menu_text'] ?? 'help_menu_text';
$help_button_text1 = $translate['help_button_text1'] ?? 'help_button_text1';
$help_button_data1 = $translate['help_button_data1'] ?? 'help_button_data1';
$help_button_text2 = $translate['help_button_text2'] ?? 'help_button_text2';
$help_button_data2 = $translate['help_button_data2'] ?? 'help_button_data2';
$help_button_text3 = $translate['help_button_text3'] ?? 'help_button_text3';
$help_button_data3 = $translate['help_button_data3'] ?? 'help_button_data3';
$shabbat_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$shabbat_back_data = $translate['shabbat_back_data'] ?? 'shabbat_back_data';

$bot_API_markup[] = [['text'=>$help_button_text1,'callback_data'=>$help_button_data1]];
$bot_API_markup[] = [['text'=>$help_button_text2,'callback_data'=>$help_button_data2]];
$bot_API_markup[] = [['text'=>$help_button_text3,'callback_data'=>$help_button_data3]];
$bot_API_markup[] = [['text'=>$shabbat_back_txt,'callback_data'=>$shabbat_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $helptext, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);

        } catch (\Throwable $e) {}
		}

    public function BasicRules(int $senderId, int $msgid): void {
		try{
		$me = $this->context->getSelf();
        $me_username = $me['username'];
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$helptext = $translate['basic_rules_text'] ?? 'basic_rules_text';
$help_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$help_back_data = $translate['welcome_button3_data'] ?? 'welcome_button3_data';
$bot_API_markup[] = [['text'=>$help_back_txt,'callback_data'=>$help_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $helptext, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);
        } catch (\Throwable $e) {}
		}

    public function SettingsInfo(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$helptext = $translate['admin_commands_text'] ?? 'admin_commands_text';
$help_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$help_back_data = $translate['welcome_button3_data'] ?? 'welcome_button3_data';
$bot_API_markup[] = [['text'=>$help_back_txt,'callback_data'=>$help_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $helptext, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);
        } catch (\Throwable $e) {}
		}

    public function CommandsAllUsers(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$helptext = $translate['all_users_commands_text'] ?? 'all_users_commands_text';
$help_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$help_back_data = $translate['welcome_button3_data'] ?? 'welcome_button3_data';
$bot_API_markup[] = [['text'=>$help_back_txt,'callback_data'=>$help_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $helptext, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);
        } catch (\Throwable $e) {}
		}

    public function CloseMsgCommand(int $msgid): void {
		try{
$this->context->messages->deleteMessages(
revoke: true, 
id: [$msgid]
);
     } catch (\Throwable $e) {}
}

    public function SettingsMenu(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$settings_menu = $translate['settings_menu'] ?? 'settings_menu';

$settings_button_text_3 = $translate['settings_button_text_3'] ?? 'settings_button_text_3';
$settings_button_data_3 = $translate['settings_button_data_3'] ?? 'settings_button_data_3';
$settings_button_text_4 = $translate['settings_button_text_4'] ?? 'settings_button_text_4';
$settings_button_data_4 = $translate['settings_button_data_4'] ?? 'settings_button_data_4';
$settings_button_data_2 = $translate['settings_button_data_2'] ?? 'settings_button_data_2';
$settings_button_text_2 = $translate['settings_button_text_2'] ?? 'settings_button_text_2';
$settings_button_text_1 = $translate['settings_button_text_1'] ?? 'settings_button_text_1';
$settings_button_data_1 = $translate['settings_button_data_1'] ?? 'settings_button_data_1';

$shabbat_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$shabbat_back_data = $translate['shabbat_back_data'] ?? 'shabbat_back_data';

$bot_API_markup[] = [['text'=>$settings_button_text_3,'callback_data'=>$settings_button_data_3]];
$bot_API_markup[] = [['text'=>$settings_button_text_4,'callback_data'=>$settings_button_data_4]];
$bot_API_markup[] = [['text'=>$settings_button_text_2,'callback_data'=>$settings_button_data_2]];
$bot_API_markup[] = [['text'=>$settings_button_text_1,'callback_data'=>$settings_button_data_1]];
$bot_API_markup[] = [['text'=>$shabbat_back_txt,'callback_data'=>$shabbat_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $settings_menu, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);

        } catch (\Throwable $e) {
			
        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: $e->getMessage(), 
            reply_markup: $bot_API_markup
        );  
		
		
		}
		}

    public function SettingsLocation(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$SettingsLocation = $translate['settingslocation'] ?? 'settingslocation';
$settingslocationtext1 = $translate['settingslocationtext1'] ?? 'settingslocationtext1';

$settings_button_back_text = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$settings_button_back_data = $translate['welcome_button5_data'] ?? 'welcome_button5_data';

$inlineQueryPeerTypeSameBotPM = ['_' => 'inlineQueryPeerTypeSameBotPM'];
$keyboardButtonSwitchInline = ['_' => 'keyboardButtonSwitchInline', 'same_peer' => true, 'text' => $settingslocationtext1, 'query' => 'ירושלים', 'peer_types' => [$inlineQueryPeerTypeSameBotPM]];
$keyboardButtonData = ['_' => 'keyboardButtonCallback', 'text' => $settings_button_back_text, 'data' => "$settings_button_back_data"];
$keyboardButtonRow1 = ['_' => 'keyboardButtonRow', 'buttons' => [$keyboardButtonSwitchInline]];
$keyboardButtonRow2 = ['_' => 'keyboardButtonRow', 'buttons' => [$keyboardButtonData]];
$bot_API_markup = ['_' => 'replyInlineMarkup', 'rows' => [$keyboardButtonRow1, $keyboardButtonRow2]];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $SettingsLocation, 
reply_markup: $bot_API_markup, 
parse_mode: 'HTML'
);

        } catch (\Throwable $e) {
			
        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: $e->getMessage(), 
            reply_markup: null
        );  
		
		
		}
		}

    public function SettingsLanguage(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$language_choose = $translate['language_choose'] ?? 'language_choose';

$languageButtons = (new Lang($this->context))->getLanguageButtons(); 
	
$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $language_choose, 
reply_markup: $languageButtons,
parse_mode: 'HTML'
);

        } catch (\Throwable $e) {
			
        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: $e->getMessage(), 
            reply_markup: null
        );  
		
		
		}
		}

    public function SettingsCandles(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$settings_candles = $translate['settings_candles'] ?? 'settings_candles';
$shabbat_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$shabbat_back_data = $translate['welcome_button5_data'] ?? 'welcome_button5_data';

$bot_API_markup[] = [['text'=>'10','callback_data'=>'setcandles_11'],['text'=>'15','callback_data'=>'setcandles_16'],['text'=>'18','callback_data'=>'setcandles_19']];
$bot_API_markup[] = [['text'=>'20','callback_data'=>'setcandles_21'],['text'=>'22','callback_data'=>'setcandles_23'],['text'=>'30','callback_data'=>'setcandles_31'],['text'=>'40','callback_data'=>'setcandles_41']];
$bot_API_markup[] = [['text'=>$shabbat_back_txt,'callback_data'=>$shabbat_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $settings_candles, 
reply_markup: $bot_API_markup,
parse_mode: 'HTML'
);

        } catch (\Throwable $e) {
			
        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: $e->getMessage(), 
            reply_markup: null
        );  
		
		
		}
		}

    public function SettingsHavdalah(int $senderId, int $msgid): void {
		try{
$lang = (new Lang($this->context))->getUserLang($senderId);
$translate = (new Lang($this->context))->loadTranslations($lang); 
$settings_havdalah = $translate['settings_havdalah'] ?? 'settings_havdalah';
$shabbat_back_txt = $translate['shabbat_back_txt'] ?? 'shabbat_back_txt';
$shabbat_back_data = $translate['welcome_button5_data'] ?? 'welcome_button5_data';

$havdalah_button_text_1 = $translate['havdalah_button_text_1'] ?? 'צאת הכוכבים [8.5 מעלות]';
$havdalah_button_text_2 = $translate['havdalah_button_text_2'] ?? 'צאת הכוכבים [42 דקות]';
$havdalah_button_text_3 = $translate['havdalah_button_text_3'] ?? 'צאת הכוכבים [72 דקות]';

$bot_API_markup[] = [['text'=>$havdalah_button_text_1,'callback_data'=>'sethavdalah_8.5']];
$bot_API_markup[] = [['text'=>$havdalah_button_text_2,'callback_data'=>'sethavdalah_42']];
$bot_API_markup[] = [['text'=>$havdalah_button_text_3,'callback_data'=>'sethavdalah_72']];
$bot_API_markup[] = [['text'=>$shabbat_back_txt,'callback_data'=>$shabbat_back_data]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->context->messages->editMessage(
peer: $senderId, 
id: $msgid, 
message: $settings_havdalah, 
reply_markup: $bot_API_markup,
parse_mode: 'HTML'
);

        } catch (\Throwable $e) {
			
        $this->context->messages->editMessage(
            peer: $senderId, 
            id: $msgid, 
            message: $e->getMessage(), 
            reply_markup: null
        );  
		
		
		}
		}

}
